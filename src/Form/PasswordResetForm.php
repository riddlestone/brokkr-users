<?php

namespace Riddlestone\Brokkr\Users\Form;

use Laminas\Filter\StringTrim;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;
use Laminas\InputFilter\InputFilterProviderInterface;
use Laminas\Validator\Identical;
use Laminas\Validator\StringLength;

class PasswordResetForm extends Form implements InputFilterProviderInterface
{
    public function init()
    {
        $this->add(
            [
                'name' => 'password',
                'type' => Password::class,
                'options' => [
                    'label' => 'New Password',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'repeat_password',
                'type' => Password::class,
                'options' => [
                    'label' => 'Retype New Password',
                ],
            ]
        );

        $this->add(
            [
                'name' => 'submit',
                'type' => Submit::class,
                'options' => [
                    'label' => 'Reset Password',
                ],
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getInputFilterSpecification()
    {
        return [
            'password' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                ],
                'validators' => [
                    [
                        'name' => StringLength::class,
                        'options' => [
                            'min' => 8,
                        ],
                    ],
                ],
            ],
            'repeat_password' => [
                'required' => true,
                'filters' => [
                    [
                        'name' => StringTrim::class,
                    ],
                ],
                'validators' => [
                    [
                        'name' => Identical::class,
                        'options' => [
                            'token' => 'password',
                        ],
                    ],
                ],
            ],
        ];
    }
}
