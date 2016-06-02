<?php

/*
 * This file is part of the Сáша framework.
 *
 * (c) tchiotludo <http://github.com/tchiotludo>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare (strict_types=1);

/**
 * Сáша frameworks tests
 *
 * @author tchiotludo <http://github.com/tchiotludo>
 */
namespace CawaTest\Html\Form;

use Cawa\App\HttpFactory;
use Cawa\Html\Forms\Fields\AbstractField;
use Cawa\Html\Forms\Fields\Text;
use Cawa\Html\Forms\Form;
use PHPUnit_Framework_TestCase as TestCase;

class UriTest extends TestCase
{
    use HttpFactory;

    /**
     * Test that parsing and composing a valid URI returns the same URI
     *
     * @param array $posts
     * @param AbstractField[] $fields
     * @param string $getValueName
     * @param array $expected
     *
     * @dataProvider formValueProvider
     */
    public function testGetValue(array $posts, array $fields, string $getValueName, array $expected = null)
    {
        $this->request()->setPosts($posts);
        $this->request()->setMethod('POST');
        $form = new Form();
        foreach ($fields as $field) {
            $form->add($field);
        }

        if (!is_null($expected) && $posts[$getValueName] === '') {
            $this->assertNull($form->getValue($getValueName));
        } else {
            $this->assertEquals($form->getValue($getValueName), is_null($expected) ? $posts[$getValueName] : $expected);
        }
    }


    /**
     * @return array
     */
    public function formValueProvider()
    {
        return [
            [
                ['name' => 'text'],
                [Text::create('name')],
                'name'
            ],

            [
                ['name' => ''],
                [Text::create('name')],
                'name'
            ],

            [
                ['name' => ['text']],
                [Text::create('name[]')],
                'name'
            ],

            [
                [
                    'name' => [
                        'key' => [
                            1 => 'text',
                            2 => 'text',
                        ],
                    ],
                ],
                [
                    Text::create('name[key][1]'),
                    Text::create('name[key][2]'),
                ],
                'name',
            ],

            [
                ['name' => ['text1','text2']],
                [Text::create('name[]'), Text::create('name[]')],
                'name',
            ],

            [
                ['name' => ['key' => ['text1','text2']]],
                [Text::create('name[key][]'), Text::create('name[key][]')],
                'name',
            ],

            [
                ['name' => ['text1', 'text2']],
                [Text::create('name[]')],
                'name',
                ['text1'],
            ],
        ];
    }

}
