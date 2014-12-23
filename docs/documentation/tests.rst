Tests
=====

Controller test case
~~~~~~~~~~~~~~~~~~~~

Ouzo provides ``ControllerTestCase`` which allows you to verify that:

* there's a route for a given url
* controllers methods work as expected
* views are rendered without errors

.. code-block:: php

    <?php
    class UsersControllerTest extends ControllerTestCase
    {
        /**
         * @test
         */
        public function shouldRenderIndex()
        {
            //when
            $this->get('/users');

            //then
            $this->assertRenders('Users/index');
        }

        /**
         * @test
         */
        public function shouldRedirectToIndexOnSuccessInCreate()
        {
            //when
            $this->post('/users', [
                'user' => [
                    'login' => 'login'
                ]]
            );

            //then
            $this->assertRedirectsTo(usersPath());
        }
    }

Methods:
--------

* ``get($url)`` - mock GET request for given url
* ``post($url, $data)`` - mock POST request with data for given url
* ``put($url, $data)`` - mock PUT request with data for given url
* ``patch($url)`` - mock PATCH request for given url
* ``delete($url)``- mock DELETE request for given url
* ``getAssigned($name)`` - get value for the current controller action
* ``getRenderedJsonAsArray()`` - get returned JSON as array
* ``getResponseHeaders()`` - get all response header

Assertions:
-----------

* ``assertRedirectsTo($path)``
* ``assertRenders($viewName)`` - asserts that the given view was rendered
* ``assertAssignsModel($variable, $modelObject)`` - asserts that a model object was assigned to a view
* ``assertDownloadsFile($file)``
* ``assertAssignsValue($variable, $value)``
* ``assertRenderedContent()`` - returns StringAssert for rendered content.
* ``assertRenderedJsonAttributeEquals($attribute, $equals)``
* ``assertResponseHeader($expected)``

----

Database test case
~~~~~~~~~~~~~~~~~~

Ouzo provides ``DbTransactionalTestCase`` class that takes care of transactions in tests.
This class starts a new transaction before each test case and rolls it back afterwards.

.. code-block:: php

    <?php
    class UserTest extends DbTransactionalTestCase
    {
        /**
         * @test
         */
        public function shouldPersistUser()
        {
            //given
            $user = new User(['name' => 'bob']);

            //when
            $user->insert();

            //then
            $storedUser = User::where(['name' => 'bob'])->fetch();
            $this->assertEquals('bob', $storedUser->name);
        }
    }

----

Model assertions
~~~~~~~~~~~~~~~~

``Assert::thatModel`` allows you to check if two model objects are equal.

Sample usage:
-------------

.. code-block:: php

    <?php
    class UserTest extends DbTransactionalTestCase
    {
        /**
         * @test
         */
        public function shouldPersistUser()
        {
            //given
            $user = new User(['name' => 'bob']);

            //when
            $user->insert();

            //then
            $storedUser = User::where(['name' => 'bob'])->fetch();
            Assert::thatModel($storedUser)->isEqualTo($user);
        }
    }

Assertions:
-----------

* ``isEqualTo($expected)`` - compares all attributes. If one model has loaded a relation and other has not, they are considered not equal. Attributes not listed in model's fields are also compared
* ``hasSameAttributesAs($expected)`` - compares only attributes listed in Models fields

----

String assertions
~~~~~~~~~~~~~~~~~

``Assert::thatString`` allows you to check strings as a fluent assertions.

Sample usage:
-------------

::

    Assert::thatString("Frodo")
         ->startsWith("Fro")->endsWith("do")
         ->contains("rod")->doesNotContain("fro")
         ->hasSize(5);

    Assert::thatString("Frodo")->matches('/Fro\w+/');
    Assert::thatString("Frodo")->isEqualToIgnoringCase("frodo");
    Assert::thatString("Frodo")->isEqualTo("Frodo");
    Assert::thatString("Frodo")->isEqualNotTo("asd");

Assertions:
-----------

* ``contains($substring)`` - check that string contains substring
* ``doesNotContain($substring)`` - check that string does not contains substring
* ``startsWith($prefix)`` - check that string is start with prefix
* ``endsWith($postfix)`` - check that string is end with postfix
* ``isEqualTo($string)`` - check that string is equal to expected
* ``isEqualToIgnoringCase($string)`` - check that string is equal to expected (case insensitive)
* ``isNotEqualTo($string)`` - check that string not equal to expected
* ``matches($regex)`` - check that string is fit to regexp
* ``hasSize($length)`` - check string length
* ``isNull()`` - check a string is null
* ``isNotNull()`` - check a string is not null
* ``isEmpty()`` - check a string is empty
* ``isNotEmpty()`` - check a string is not empty

----

Array assertions
~~~~~~~~~~~~~~~~

``Assert::thatArray`` is a fluent array assertion to simplify your tests.

Sample usage:
-------------

.. code-block:: php

    <?php
    $animals = ['cat', 'dog', 'pig'];
    Assert::thatArray($animals)->hasSize(3)->contains('cat');
    Assert::thatArray($animals)->containsOnly('pig', 'dog', 'cat');
    Assert::thatArray($animals)->containsExactly('cat', 'dog', 'pig');

.. note::

    Array assertions can also be used to examine array of objects. Methods to do this is ``onProperty`` and ``onMethod``.

Using ``onProperty``:

.. code-block:: php

    <?php
    $object1 = new stdClass();
    $object1->prop = 1;

    $object2 = new stdClass();
    $object2->prop = 2;

    $array = [$object1, $object2];
    Assert::thatArray($array)->onProperty('prop')->contains(1, 2);

Using ``onMethod``:

::

    Assert::thatArray($users)->onMethod('getAge')->contains(35, 24);

Assertions:
-----------

* ``contains($element ..)`` - vararg elements to examine that array contains them
* ``containsOnly($element ..)`` - vararg elements to examine that array contains **only** them
* ``containsExactly($element ..)`` - vararg elements to examine that array contain **exactly** elements in pass order
* ``hasSize($expectedSize)`` - check size of the array
* ``isNotNull()`` - check the array is not null
* ``isEmpty()`` - check the array is empty
* ``isNotEmpty()`` - check the array is not empty
* ``containsKeyAndValue($elements)``
* ``containsSequence($element ..)`` - check that vararg sequence is exists in the array
* ``exclude($element ..)``
* ``hasEqualKeysRecursively(array $array)``

----

Exception assertions
~~~~~~~~~~~~~~~~~~~~

CatchException enables you to write a unit test that checks that an exception is thrown.

Sample usage:
-------------

::

    //given
    $foo = new Foo();

    //when
    CatchException::when($foo)->method();

    //then
    CatchException::assertThat()->isInstanceOf("FooException");

Assertions:
-----------

* ``isInstanceOf($exception)``
* ``isEqualTo($exception)``
* ``notCaught()``
* ``hasMessage($message)``

----

Mocking
~~~~~~~

Ouzo provides a mockito like mocking library that allows you to write tests in BDD or AAA (arrange act assert) fashion.

You can stub method calls:

::

    $mock = Mock::mock();
    Mock::when($mock)->method(1)->thenReturn('result');

    $result = $mock->method(1);

    $this->assertEquals("result", $result);

And then verify interactions:

::

    //given
    $mock = Mock::mock();

    //when
    $mock->method("arg");

    //then
    Mock::verify($mock)->method("arg");

Unlike other PHP mocking libraries you can verify interactions ex post facto which is more natural and fits BDD or AAA style.

If you use type hinting and the mock has to be of a type of a Class, you can pass the required type to ``Mock::mock`` method.

::

    $mock = Mock::mock('Foo');

    $this->assertTrue($mock instanceof Foo);

You can stub a method to throw an exception;

::

    Mock::when($mock)->method()->thenThrow(new Exception());

Verification that a method was not called:

::

    Mock::verify($mock)->neverReceived()->method("arg");

Argument matchers:

* Mock::any() - matches any value for an argument at the given position

::

    Mock::verify($mock)->method(1, Mock::any(), "foo");

* Mock::anyArgList() - matches any possible arguments. It means that all calls to a given method will be matched.

::

    Mock::verify($mock)->method(Mock::anyArgList());

You can stub multiple calls in one call to thenReturn:

::

    $mock = Mock::mock();
    Mock::when($mock)->method(1)->thenReturn('result1', 'result2');
    Mock::when($mock)->method()->thenThrow(new Exception('1'), new Exception('2'));

You can stub a method to return value calculated by a callback function:

::

    Mock::when($mock)->method(Mock::any())->thenAnswer(function (MethodCall $methodCall) {
      return $methodCall->name . ' ' . Arrays::first($methodCall->arguments);
    });