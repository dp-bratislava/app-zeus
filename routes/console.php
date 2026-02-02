<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('datahub:update')->hourly();