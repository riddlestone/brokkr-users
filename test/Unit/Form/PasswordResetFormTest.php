<?php

namespace Riddlestone\Brokkr\Users\Test\Unit\Form;

use PHPUnit\Framework\TestCase;
use Riddlestone\Brokkr\Users\Form\PasswordResetForm;

class PasswordResetFormTest extends TestCase
{
    public function testInit()
    {
        $form = new PasswordResetForm();
        $form->init();
        $this->assertCount(3, $form->getElements());
    }

    public function isValidData()
    {
        return [
            [
                'data' => [],
                'valid' => false,
            ],
            [
                'data' => [
                    'password' => 'MySuperS3cretP@ssword',
                ],
                'valid' => false,
            ],
            [
                'data' => [
                    'password' => 'MySuperS3cretP@ssword',
                    'repeat_password' => 'ADifferentSuperS3cretP@ssword',
                ],
                'valid' => false,
            ],
            [
                'data' => [
                    'password' => 'short',
                    'repeat_password' => 'short',
                ],
                'valid' => false,
            ],
            [
                'data' => [
                    'password' => 'MySuperS3cretP@ssword',
                    'repeat_password' => 'MySuperS3cretP@ssword',
                ],
                'valid' => true,
            ],
        ];
    }

    /**
     * @dataProvider isValidData
     * @param array $data
     * @param bool $valid
     */
    public function testIsValid(array $data, bool $valid)
    {
        $form = new PasswordResetForm();
        $form->init();
        $form->setData($data);
        $this->assertEquals($valid, $form->isValid());
    }
}
