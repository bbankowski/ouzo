<?php

use Model\Product;
use Ouzo\Tests\DbTransactionalTestCase;
use Ouzo\View;

include_once ROOT_PATH . '/test/locales/Pl.php';

class FormHelperTest extends DbTransactionalTestCase
{
    function setUp()
    {
        parent::setUp();
        new View('test');
    }

    /**
     * @test
     */
    function shouldReturnSelectFieldWithSelectedOption()
    {
        // when
        $result = selectField('Gender', 'gender', 'M', array('M' => 'male', 'F' => 'female'));

        // then
        $this->assertContains('value="M" selected', $result);
    }

    /**
     * @test
     */
    function shouldReturnSelectFieldWhenOnlyValuesAreGiven()
    {
        // when
        $result = selectField('Gender', 'gender', '', array('male', 'female'));

        // then
        $this->assertContains('<option value="0" >male</option>', $result);
        $this->assertContains('<option value="1" >female</option>', $result);
    }

    /**
     * @test
     */
    public function shouldDisplayTextField()
    {
        // when
        $result = textField('Gender', 'gender', 'val');

        // then
        $expectedHtml = <<<HTML
        <div class="field">
            <label for="gender">Gender</label>
            <input type="text" value="val" id="gender" name="gender" style=""/>
        </div>
HTML;
        $this->assertEquals($expectedHtml, $result);
    }

    /**
     * @test
     */
    public function shouldUseNameAsDefaultIdInTextField()
    {
        // when
        $result = textField('Gender', 'gender[]', '');

        // then
        $this->assertContains('<input type="text" value="" id="gender_"', $result);
    }

    /**
     * @test
     */
    public function shouldUseGivenIdInTextField()
    {
        // when
        $result = textField('Gender', 'gender[]', '', array('id' => 'xyz'));

        // then
        $this->assertContains('<input type="text" value="" id="xyz"', $result);
    }

    /**
     * @test
     */
    public function shouldUseClassInTextField()
    {
        // when
        $result = textField('Gender', 'gender', '', array('class' => 'xyz'));

        // then
        $this->assertContains('<div class="xyz field"', $result);
    }

    /**
     * @test
     */
    public function shouldAppendErrorClassIfErrorInTextField()
    {
        // when
        $result = textField('Gender', 'gender', '', array('class' => 'xyz', 'error' => true));

        // then
        $this->assertContains('<div class="xyz field field-with-error"', $result);
    }


    /**
     * @test
     */
    public function shouldUseGivenCustomHTMLAttributes()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('custom_attribute' => 'custom_value'));

        //then
        $this->assertContains('<input type="text" value="val" id="gender" name="gender" style="" custom_attribute="custom_value"', $result);
    }

    /**
     * @test
     */
    public function shouldAddReadOnly()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('readonly' => true));

        //then
        $this->assertContains('<input type="text" value="val" id="gender" name="gender" style="" readonly="1"', $result);
    }

    /**
     * @test
     */
    public function shouldNotAddReadOnlyIfFalse()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('readonly' => false));

        //then
        $this->assertNotContains('readonly', $result);
    }

    /**
     * @test
     */
    public function shouldNotAddReadOnlyNoReadOnlyOption()
    {
        //when
        $result = textField('Gender', 'gender', 'val');

        //then
        $this->assertNotContains('readonly', $result);
    }

    /**
     * @test
     */
    public function shouldGenerateTextArea()
    {
        //given
        //when
        $result = textArea('Label', 'label', 'value', array('cols' => 12, 'rows' => 11));

        //then
        $expected = <<<HTML
        <div class="field">
            <label for="label">Label</label>
            <textarea name="label" id="label" rows="11" cols="12" style="">value</textarea>
        </div>
HTML;
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateLabelWithAttributes()
    {
        //when
        $result = textField('Gender', 'gender', 'val', array('label_width' => 10));

        //then
        $expectedHtml = <<<HTML
        <div class="field">
            <label for="gender" style="margin-left: px; width: 10px;">Gender</label>
            <input type="text" value="val" id="gender" name="gender" style=""/>
        </div>
HTML;
        $this->assertEquals($expectedHtml, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateCheckbox()
    {
        //when
        $result = checkboxField('Gender', 'gender', 'val', true, array('label_width' => 10));

        //then
        $expectedHtml = <<<HTML
        <div class="field">
            <label for="gender" style="margin-left: px; width: 10px;">Gender</label>
            <input type="checkbox" value="val" id="gender" name="gender" checked/>
        </div>
HTML;
        $this->assertEquals($expectedHtml, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateHiddenField()
    {
        //when
        $result = hiddenField(array('name' => "Name", 'value' => 'val', 'id' => 'name'));

        //then
        $expected = <<<HTML
        <input type="hidden" value="val" id="name" name="Name"/>
HTML;
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateSelectField()
    {
        //when
        $result = selectField('Label', 'lab', 2, array(1 => 'Opt1', 2 => 'Opt1'));

        //then
        $expected = <<<HTML
        <div class="field">
            <label for="lab">Label</label>
            <select id="lab" name="lab" size="1"><option value="1" >Opt1</option><option value="2" selected>Opt1</option></select>
        </div>
HTML;
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateSelectTag()
    {
        //given
        $items = array(1 => 'Opt1', 2 => 'Opt1');
        $attributes = array('id' => "lab", 'name' => "lab", 'size' => "1");

        //when
        $result = selectTag($items, array(2), $attributes);

        //then
        $expected = '<select id="lab" name="lab" size="1"><option value="1" >Opt1</option><option value="2" selected>Opt1</option></select>';
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldGenerateMultiselectField()
    {
        //when
        $result = multiselectField('Label', 'lab', array(1, 2), array(1 => 'Opt1', 2 => 'Opt2', 3 => 'Opt3'), array('size' => 2, 'class' => 'className'));

        //then
        $expected = <<<HTML
        <div class="field">
            <label for="lab">Label</label>
            <select id="lab" name="lab[]" multiple="multiple" size="2" class="className"><option value="1" selected>Opt1</option><option value="2" selected>Opt2</option><option value="3" >Opt3</option></select>
        </div>
HTML;
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function shouldCreateTextFieldInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product, '', array('auto_labels' => false));

        //when
        $textField1 = $form->textField('name');
        $textField2 = $form->textField('name', array('id' => 'id_new'));
        $textField3 = $form->textField('name', array('style' => 'color: red;'));

        //then
        $this->assertEquals('<input type="text" value="name" id="product_name" name="product[name]"/>', $textField1);
        $this->assertContains('id="id_new"', $textField2);
        $this->assertContains('style="color: red;"', $textField3);
    }

    /**
     * @test
     */
    public function shouldCreateTextAreaInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product, '', array('auto_labels' => false));

        //when
        $textArea1 = $form->textArea('name');
        $textField2 = $form->textField('name', array('id' => 'id_new'));
        $textField3 = $form->textField('name', array('rows' => 12, 'cols' => 10, 'style' => 'color: red;'));

        //then
        $this->assertEquals('<textarea id="product_name" name="product[name]">name</textarea>', $textArea1);
        $this->assertContains('id="id_new"', $textField2);
        $this->assertContains('rows="12" cols="10" style="color: red;"', $textField3);
    }

    /**
     * @test
     */
    public function shouldCreateSelectFieldInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $categories = array(1 => 'Cat1', 2 => 'Cat2');
        $form = formFor($product, '', array('auto_labels' => false));

        //when
        $selectField1 = $form->selectField('id_category', $categories);
        $selectField2 = $form->selectField('name', $categories, array('id' => 'id_new'));

        //then
        $this->assertEquals(
            '<select id="product_id_category" name="product[id_category]"><option value="1" selected>Cat1</option><option value="2" >Cat2</option></select>',
            $selectField1
        );
        $this->assertContains('id="id_new"', $selectField2);
    }

    /**
     * @test
     */
    public function shouldCreateHiddenFieldInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product, '', array('auto_labels' => false));

        //when
        $result1 = $form->hiddenField('name');
        $result2 = $form->hiddenField('name', 'new_name');

        //then
        $this->assertEquals('<input type="hidden" value="name" id="product_name" name="product[name]"/>', $result1);
        $this->assertEquals('<input type="hidden" value="new_name" id="product_name" name="product[name]"/>', $result2);
    }

    /**
     * @test
     */
    public function shouldCreateLabelInFormForModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));
        $form = formFor($product, '', array('auto_labels' => false));

        //when
        $result1 = $form->label('name');
        $result2 = $form->label('description');

        //then
        $this->assertEquals('<label for="product_name">product.name</label>', $result1);
        $this->assertEquals('<label for="product_description">Product description</label>', $result2);
    }

    /**
     * @test
     */
    public function shouldCreatePasswordFieldInFormModelClass()
    {
        //given
        $product = new Product(array('description' => 'desc', 'name' => 'name', 'id_category' => 1));

        //when
        $result = formFor($product, '', array('auto_labels' => false))->passwordField('name');

        //then
        $this->assertEquals('<input type="password" value="name" id="product_name" name="product[name]"/>', $result);
    }

    /**
     * @test
     * @dataProvider requestUnsupportedMethods
     */
    public function shouldCreateWorkAroundForUnsupportedMethods($method)
    {
        //when
        $form = formTag('/users/add', $method);

        //then
        $this->assertContains('method="POST"', $form);
        $this->assertContains('value="' . $method . '" name="_method"', $form);
    }

    /**
     * @test
     * @dataProvider requestSupportedMethods
     */
    public function shouldNoCreateWorkAroundWhenSupportedMethods($method)
    {
        //when
        $form = formTag('/users/add', $method);

        //then
        $this->assertContains('method="' . $method . '"', $form);
        $this->assertNotContains('value="' . $method . '" name="_method"', $form);
    }

    public function requestUnsupportedMethods()
    {
        return array(
            array('PUT'),
            array('PATCH'),
            array('DELETE')
        );
    }

    public function requestSupportedMethods()
    {
        return array(
            array('POST'),
            array('GET')
        );
    }
}