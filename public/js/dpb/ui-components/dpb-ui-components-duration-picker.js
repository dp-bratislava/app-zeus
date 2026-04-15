window.durationPicker = function ({ state }) {
    return {
        state: state,
        stateBuffer: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        focusedField: null,
        multipliers: { hours: 3600, minutes: 60, seconds: 1 },
        init() {
            this.stateBuffer = Number(this.state || 0);
            this.splitState();
        },

        adjustTime(delta, event) {
            if (!this.focusedField) return;
            const step = event.shiftKey ? 5 : 1;
            const change = Number(delta || 0) * step * (this.multipliers[this.focusedField] || 0);
            this.stateBuffer = Math.max(0, Number(this.stateBuffer || 0) + change);
            this.updateState();
            this.splitState();
        },

        handleManualInput(event) {
            clearTimeout(this.timeout);

            let baseTotal = Number(this.state || 0);
            const inputVal = Number(event.target.value || 0) * (this.multipliers[this.focusedField] || 0);

            if (this.focusedField === 'hours') {
                baseTotal %= 3600;
            } else if (this.focusedField === 'minutes') {
                baseTotal = (Math.floor(baseTotal / 3600) * 3600) + (baseTotal % 60);
            } else if (this.focusedField === 'seconds') {
                baseTotal = Math.floor(baseTotal / 60) * 60;    // not possible yet
            }

            const calculatedTotal = Math.max(0, baseTotal + inputVal);

            this.timeout = setTimeout(() => {
                this.stateBuffer = calculatedTotal;
                this.updateState();
                this.splitState();
            }, 1000);
        },

        splitState() {
            const total = parseInt(this.stateBuffer || 0);
            this.hours = Math.floor(total / 3600);
            this.minutes = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
            if (this.$refs.hoursInput && this.$refs.hoursInput.value != this.hours) {
                this.$refs.hoursInput.value = this.hours;
            }
            
            if (this.$refs.minutesInput && this.$refs.minutesInput.value != this.minutes) {
                this.$refs.minutesInput.value = this.minutes;
            }
        },

        updateState() {
            this.state = Number(this.stateBuffer || 0);
        },

        handleBackspace(event) {
            if (this.focusedField !== 'minutes' || event.target.value.length > 0) return;
            if (event.key === 'Backspace') {
                if (this.$refs.hoursInput) {
                    this.$refs.hoursInput.focus();
                    const len = this.$refs.hoursInput.value.length;
                    this.$refs.hoursInput.setSelectionRange(len, len);
                }
            }
        },

        handleNavigation(event) {
            if (!event.shiftKey || (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight')) return;
            event.preventDefault();
            if (event.key === 'ArrowLeft' && this.focusedField === 'minutes') {
                if (this.$refs.hoursInput) this.$refs.hoursInput.focus();
            } else if (event.key === 'ArrowRight' && this.focusedField === 'hours') {
                if (this.$refs.minutesInput) this.$refs.minutesInput.focus();
            }
        },

        setState(newDuration) {
            this.stateBuffer = Number(newDuration || 0);
            this.splitState();
            this.$nextTick(() => { this.updateState(); });
        }
    }
}