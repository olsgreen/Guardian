# Guardian #
Self validating models for smarter & cleaner Laravel 4 Eloquent ORM. Inspired by [Ardent](https://github.com/laravelbook/ardent).

## Installation ##
Add our repository to your `composer.json` file like so:

	"repositories": [{
	        "type": "vcs",
	        "url": "https://github.com/olsgreen/Guardian"
	    }],

also require `Guardian`:

	"require": {
		"olsgreen/guardian": "dev-master"
	},

then run `composer update`.

## Getting started ##
Guardian extends the Eloquent model making it extremely easy to retrofit models or remove later if needed.


To get up and running all you need to do is change your model to extend from `Guardian` and add a `getValidator()` method so that `Guardian` can obtain a correctly configured instance of a `Validator`.

	use Olsgreen\Guardian\Guardian;

	class User extends Guardian {

		........
	
		/**
		 * Overridden from the base class
		 * 
		 * @return Illuminate\Validation\Validator
		 */
		public function getValidator()
		{
			// Standard rules
			$rules = array(
				'first_name' => 'required|min:3',
				'last_name' => 'required|min:3',
				'username' => 'required|unique:users,username|min:2',
				'password' => 'required|min:6',
				'email' => 'email|unique:users,email',
				);
	
			// Rules for models that already exist
			if ($this->exists) {
				$rules['username'] = 'required|unique:users,username,' . $this->id . '|min:2';
				$rules['email'] = 'email|unique:users,email,' . $this->id;
			}
	
			// Return the validator
			return Validator::make($this->attributes, $rules);
		}
	
		........

	}

## Validation Errors ##
Guardian will automatically validate the model on calling `save()`. If validation fails `save()` will `return false` otherwise it will `return true`, providing the underlying save was successful.

If the validation fails your can get the `MessageBag` containing the validation errors by calling `getValidationMessages()` on the model.

An example of saving a user within a controller:

	if (!$user->save()) {
		return Redirect::route('user.form')->withErrors($user->getValidationMessages());
	}

## Force Save ##
You can force a save, completely bypassing validation, by calling `forceSave()` on a model.

## Sidestep MassAssignmentExceptions with fillFillable()
`fillFillable()` allows you to quickly and easily fill a model with an array of attributes. This method conveintly sidesteps the `MassAssignmentException` generated when trying to use the `fill()` method with `Input::all()`. A usage example is below:

	$user = User::find(1);
	$user->fillFillable(Input::all());
	$user->save();