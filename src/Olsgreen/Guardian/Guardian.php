<?php namespace Olsgreen\Guardian;

/**
 * Guardian
 * A simple base class for self validating models 
 * for Laravel 4.x and Eloquent.
 * 
 * @author Oliver Green <green2go@gmail.com>
 * @version 1.0.0
 * @license http://opensource.org/licenses/MIT
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Validator;
use Illuminate\Support\MessageBag;

abstract class Guardian extends Model {

	/**
	 * Validation message holder
	 * 
	 * @var Illuminate\Support\MessageBag
	 */
	public $validation_messages;

	/**
	 * Constructor
	 * 
	 * @param array $attributes
	 */
	public function __construct(array $attributes = array())
	{
		$this->validation_messages = new MessageBag;
		parent::__construct($attributes);
	}

	/**
	 * Returns a validator instance that 
	 * will be used to validate the model.
	 * 
	 * @return Illuminate\Validation\Validator
	 */
	public function getValidator()
	{
		return Validator::make($this->attributes, array());
	}

	/**
	 * Accessor method for the 
	 * validator message bag.
	 * 
	 * @return Illuminate\Support\MessageBag
	 */
	public function getValidationMessages()
	{
		return $this->validation_messages;
	}

	/**
	 * Overriden save method
	 * Runs the validation and returns false 
	 * if validation failed.
	 * 
	 * @param  array  $options
	 * @return boolean
	 */
	public function save(array $options = array())
	{
		$result = false;

		// Get the validator
		$validator = $this->getValidator();

		// Validate the model
		if (!$validator->fails()) {
			$result = parent::save($options);
		} else {
			$this->validation_messages = $validator->messages();
		}

		return $result;
	}

	/**
	 * Saves the model without running any validation.
	 * 
	 * @param  array  $options
	 * @return boolean
	 */
	public function forceSave(array $options = array())
	{
		return parent::save($options);
	}

    /**
    * Fill the model with an array of attributes using 
    * only the values that are mass assignable.
    *
    * @param  array  $attributes
    * @return \Illuminate\Database\Eloquent\Model
    */
	public function fillFillable(array $attributes = array())
	{
		// Remove attributes not specified as fillable
		if (is_array($this->fillable) && count($this->fillable) > 0 && '*' != $this->fillable[0]) {
				$attributes = array_intersect_key($attributes, array_flip($this->fillable));
		}

		if (is_array($this->guarded) && count($this->guarded) > 0) {
			// Remove any attributes specified as guarded
			if ('*' != $this->guarded[0]) {
				$attributes = array_diff_key($attributes, array_flip($this->guarded));
			} elseif ('*' === $this->guarded[0] && is_array($this->fillable) && count($this->fillable) === 0) {
				$attributes = array(); // Remove all attributes if fillable isn't defined and guarded is specified as all
			}
		}
		
		return $this->fill($attributes);
	}

}