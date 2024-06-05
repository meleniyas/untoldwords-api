<?php

namespace App\Service;

use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

// Service for validate data agains a constraint
class Validator
{
    public function validate(?array $dataToValidate = [], Collection $constraint): array
    {
    
        $validator = Validation::createValidator();
        $violationList = $validator->validate($dataToValidate, $constraint);

        $violationsInfo = [];

        if (count($violationList) > 0) {
            
            foreach ($violationList as $violation) {
                
                // Located errors by fields. Stylized
                $field = str_replace(['[', ']'], '', $violation->getPropertyPath());

                // Each error is grouped by field
                $violationsInfo[$field][] = $violation->getMessage();
            }
        
        }

        return $violationsInfo;
    }
}
