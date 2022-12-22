<?php

namespace Core\Video\Domain\Validation;

use Rakit\Validation\Validator;
use Shared\Domain\Entity\Entity;
use Shared\Domain\Notification\DTO\Input;
use Shared\Domain\Validation\ValidatorInterface;

class VideoRakitValidator implements ValidatorInterface
{
    public function validate(Entity $entity)
    {
        $data = $this->convertEntityForArray($entity);

        $validation = (new Validator())->validate($data, [
            'title' => 'required|min:3|max:255',
            'description' => 'required|min:3|max:255',
            'yearLaunched' => 'required|integer|max:' . date('Y'),
            'duration' => 'required|integer',
        ]);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $error) {
                $entity->getNotification()->addError(new Input(
                    context: 'video',
                    message: $error
                ));
            }
        }
    }

    private function convertEntityForArray(Entity $entity): array
    {
        return [
            'title' => $entity->title,
            'description' => $entity->description,
            'yearLaunched' => $entity->yearLaunched,
            'duration' => $entity->duration,
        ];
    }
}
