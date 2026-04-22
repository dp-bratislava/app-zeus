<?php

namespace App\Resolvers\Snapshots;

class WorkTaskSubjectResolver_bak
{
    public function resolve($workTaskSubject): array
    {
    // dd($workTaskSubject->attachAttributeOptions);
        return match (true) {
            // $workTaskSubject->maintainable instanceof \Dpb\WorkTimeFund\Models\Maintainables\Table => $this->busstopDisplays($workTaskSubject),
            // $workTaskSubject->maintainable instanceof \Dpb\WorkTimeFund\Models\Maintainables\Vehicle => $this->vehicle($workTaskSubject),
            !empty($workTaskSubject->attributeOptions) => $this->attributes($workTaskSubject->attributeOptions),
            default => []
        };
    }

    public function batchResolve($workTaskSubjects)
    {
        $result = [];

        if (empty($workTaskSubjects)) {
            return $result;
        }

        foreach ($workTaskSubjects as $wts) {
            $subject = $this->resolve($wts);
            if (!empty($subject)) {
                $result[] = $subject;
            }
        }

        return $result;
    }

    private function vehicle($v): array
    {
        return [
            'subject_type' => 'vehicle',
            'subject_id' => $v->id,
            'label' => $v?->getTitle(),
            'code' => $v->code->code,
            'meta' => json_encode([
                'vin' => $v->vin,
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];
    }

    private function busstopDisplays($d): array
    {
        return [
            'subject_type' => 'busstop-display',
            'subject_id' => $d->id,
            'label' => $d?->getTitle(),
            'code' => $d->serial_number,
            'meta' => json_encode([
                'location' => $d?->getTitle(),
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];
    }

    private function attributes($attributes): array
    {
        $attribute = $attributes->first();
        // dd(
        //     $attribute->label,
        //     // $attribute->type->title
        // );
        return [
            'subject_type' => $attribute->type->title,
            'subject_id' => $attribute->id,
            'label' => $attribute->label,
            'code' => null,
            'meta' => json_encode([                
            ], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];
    }    
}
