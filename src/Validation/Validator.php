<?php
namespace Src\Validation;

class Validator {
    private array $data;
    private array $rules;
    private array $errors = [];
    
    private function __construct($data, $rules) {
        $this->data = $data;
        $this->rules = $rules;
    }
    
    public static function make($data, $rules) {
        return new self($data, $rules);
    }
    
    public function fails() {
        $this->errors = [];
        
        foreach($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $rules = explode('|', $ruleString);
            
            foreach($rules as $rule) {
                if($rule === 'required' && ($value === null || $value === '')) {
                    $this->errors[$field][] = 'The ' . $field . ' field is required';
                }
                elseif($value !== null && $value !== '') {
                    if(str_starts_with($rule, 'min:')) {
                        $min = (int)substr($rule, 4);
                        if(strlen((string)$value) < $min) {
                            $this->errors[$field][] = 'The ' . $field . ' must be at least ' . $min . ' characters';
                        }
                    }
                    elseif(str_starts_with($rule, 'max:')) {
                        $max = (int)substr($rule, 4);
                        if(strlen((string)$value) > $max) {
                            $this->errors[$field][] = 'The ' . $field . ' may not be greater than ' . $max . ' characters';
                        }
                    }
                    elseif($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = 'The ' . $field . ' must be a valid email address';
                    }
                    elseif(str_starts_with($rule, 'enum:')) {
                        $options = explode(',', substr($rule, 5));
                        if(!in_array($value, $options, true)) {
                            $this->errors[$field][] = 'The ' . $field . ' must be one of: ' . implode(', ', $options);
                        }
                    }
                    elseif($rule === 'numeric' && !is_numeric($value)) {
                        $this->errors[$field][] = 'The ' . $field . ' must be a number';
                    }
                    elseif($rule === 'integer' && filter_var($value, FILTER_VALIDATE_INT) === false) {
                        $this->errors[$field][] = 'The ' . $field . ' must be an integer';
                    }
                }
            }
        }
        
        return !empty($this->errors);
    }
    
    public function errors() {
        return $this->errors;
    }
    
    public static function sanitize(array $input) {
        foreach($input as $key => $value) {
            if(is_string($value)) {
                $input[$key] = trim($value);
                // Basic XSS protection
                $input[$key] = htmlspecialchars($input[$key], ENT_QUOTES, 'UTF-8');
            }
        }
        return $input;
    }
}