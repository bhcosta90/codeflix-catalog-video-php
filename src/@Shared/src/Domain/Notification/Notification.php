<?php

namespace Costa\DomainPackage\Domain\Notification;

class Notification
{
    private $errors = [];

    public function getErrors()
    {
        return $this->errors;
    }

    public function addError(DTO\Input $input)
    {
        array_push($this->errors, [
            'context' => $input->context,
            'message' => $input->message,
        ]);
    }

    public function hasErrors(): bool
    {
        return (bool) count($this->errors);
    }

    public function message(?string $filter = null): string
    {
        $groupErrors = [];

        foreach ($this->errors as $error) {
            if (empty($filter) || $filter === $error['context']) {
                if (!isset($groupErrors[$error['context']])) {
                    $groupErrors[$error['context']] = [];
                }
                array_push($groupErrors[$error['context']], $error);
            }
        }

        $response = '';
        foreach ($groupErrors as $k => $context) {
            $response .= "{$k}: ";
            foreach ($context as $error) {
                $response .= "{$error['message']}, ";
            }
            $response = substr($response, 0, -2) . " | ";
        }
        return substr($response, 0, -3);
    }
}
