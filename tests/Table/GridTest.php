<?php namespace Orchestra\Support\Tests\Table;

class GridTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Test instanceof Orchestra\Support\Table\Grid.
	 *
	 * @test
	 * @group support
	 */
	public function testInstanceOfGrid()
	{
		$stub = new \Orchestra\Support\Table\Grid(array(
			'emptyMessage' => 'No data',
			'view'         => 'foo',
		));

		$refl         = new \ReflectionObject($stub);
		$emptyMessage = $refl->getProperty('emptyMessage');
		$view         = $refl->getProperty('view');

		$emptyMessage->setAccessible(true);
		$view->setAccessible(true);

		$this->assertInstanceOf('\Orchestra\Support\Table\Grid', $stub);
		$this->assertEquals('No data', $emptyMessage->getValue($stub));
		$this->assertEquals('foo', $view->getValue($stub));
	}

	/**
	 * Test Orchestra\Support\Table\Grid::with() method.
	 *
	 * @test
	 * @group support
	 */
	public function testWithMethod()
	{
		$mock = array(new \Illuminate\Support\Fluent);
		$stub = new \Orchestra\Support\Table\Grid(array());
		$stub->with($mock, false);

		$refl     = new \ReflectionObject($stub);
		$rows     = $refl->getProperty('rows');
		$model    = $refl->getProperty('model');
		$paginate = $refl->getProperty('paginate');

		$rows->setAccessible(true);
		$model->setAccessible(true);
		$paginate->setAccessible(true);

		$this->assertEquals($mock, $rows->getValue($stub)->data);
		$this->assertEquals($mock, $model->getValue($stub));
		$this->assertFalse($paginate->getValue($stub));
		$this->assertTrue(isset($stub->model));
	}

	/**
	 * Test Orchestra\Support\Table\Grid::layout() method.
	 *
	 * @test
	 * @group support
	 */
	public function testLayoutMethod()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$refl = new \ReflectionObject($stub);
		$view = $refl->getProperty('view');
		$view->setAccessible(true);

		$stub->layout('horizontal');
		$this->assertEquals('orchestra::support.table.horizontal', $view->getValue($stub));

		$stub->layout('vertical');
		$this->assertEquals('orchestra::support.table.vertical', $view->getValue($stub));

		$stub->layout('foo');
		$this->assertEquals('foo', $view->getValue($stub));
	}

	/**
	 * Test Orchestra\Support\Table\Grid::of() method.
	 *
	 * @test
	 * @group support
	 */
	public function testOfMethod()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());
		$expected = array(
			new \Illuminate\Support\Fluent(array(
				'id'           => 'id',
				'label'        => 'Id',
				'value'        => 'Foobar',
				'label_markup' => array(),
				'cell_markup'  => function ($row) { return array(); }
			)),
			new \Illuminate\Support\Fluent(array(
				'id'           => 'foo1',
				'label'        => 'Foo1',
				'value'        => 'Foo1 value',
				'label_markup' => array(),
				'cell_markup'  => function ($row) { return array(); }
			)),
			new \Illuminate\Support\Fluent(array(
				'id'           => 'foo2',
				'label'        => 'Foo2',
				'value'        => 'Foo2 value',
				'label_markup' => array(),
				'cell_markup'  => function ($row) { return array(); }
			))
		);

		$stub->column('id', function ($c)
		{
			$c->value('Foobar');
		});

		$stub->column(function ($c)
		{
			$c->id('foo1')->label('Foo1');
			$c->value('Foo1 value');
		});

		$stub->column('Foo2', 'foo2')->value('Foo2 value');

		$stub->markup = array('class' => 'foo');

		$output = $stub->of('id');

		$this->assertEquals('Foobar', $output->value);
		$this->assertEquals('Id', $output->label);
		$this->assertEquals('id', $output->id);

		$this->assertEquals(array('class' => 'foo'), $stub->markup);
		$this->assertEquals($expected, $stub->columns());
	}

	/**
	 * Test Orchestra\Support\Table\Grid::of() method throws exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testOfMethodThrowsException()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$output = $stub->of('id');
	}

	/**
	 * Test Orchestra\Support\Table\Grid::markup() method.
	 *
	 * @test
	 * @group support
	 */
	public function testMarkupMethod()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$refl   = new \ReflectionObject($stub);
		$markup = $refl->getProperty('markup');
		$markup->setAccessible(true);

		$stub->markup(array('class' => 'foo'));

		$this->assertEquals(array('class' => 'foo'), $markup->getValue($stub));
		$this->assertEquals(array('class' => 'foo'), $stub->markup());

		$stub->markup('id', 'foobar');

		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $markup->getValue($stub));
		$this->assertEquals(array('id' => 'foobar', 'class' => 'foo'), $stub->markup());
	}

	/**
	 * Test Orchestra\Support\Table\Grid magic method __call() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodCallThrowsException()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$stub->invalid_method();
	}

	/**
	 * Test Orchestra\Support\Table\Grid magic method __get() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodGetThrowsException()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$invalid = $stub->invalid_property;
	}

	/**
	 * Test Orchestra\Support\Table\Grid magic method __set() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodSetThrowsException()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$stub->invalid_property = array('foo');
	}

	/**
	 * Test Orchestra\Support\Table\Grid magic method __set() throws 
	 * exception when $values is not an array.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodSetThrowsExceptionValuesNotAnArray()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$stub->markup = 'foo';
	}

	/**
	 * Test Orchestra\Support\Table\Grid magic method __isset() throws 
	 * exception.
	 *
	 * @expectedException \InvalidArgumentException
	 * @group support
	 */
	public function testMagicMethodIssetThrowsException()
	{
		$stub = new \Orchestra\Support\Table\Grid(array());

		$invalid = isset($stub->invalid_property) ? true : false;
	}
}