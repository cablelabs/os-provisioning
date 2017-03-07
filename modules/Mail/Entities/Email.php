<?php namespace Modules\Mail\Entities;

class Email extends \BaseModel {

	// The associated SQL table for this Model
	public $table = 'email';

	// Name of View
	public static function view_headline()
	{
		return 'Email';
	}

	// There are no validation rules
	public static function rules($id=null)
	{
		return array(
			'localpart' => 'regex:/^[0-9A-Za-z\.\-\_]+$/|required|max:64|unique:email,localpart,'.$id.',id,deleted_at,NULL',
			'domain_id' => 'required',
			'index' => "integer|required",
			'forwardto' => 'email',
		);
	}

	// Link title in index view
	public function view_index_label()
	{
		return ['index' =>	[$this->localpart, $this->index, $this->greylisting, $this->blacklisting, $this->forwardto],
			'index_header' =>	['Local Part', 'Index', 'Greylisting', 'Blacklisting', 'Forward To'],
			'bsclass' => 'success',
			'header' => $this->index.': '.$this->localpart.'@'.$this->domain->name];
	}

	public function view_belongs_to()
	{
		return $this->contract;
	}

	public function contract()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Contract');
	}

	public function domain()
	{
		return $this->belongsTo('Modules\ProvBase\Entities\Domain');
	}

	/**
	 * Update the email password
	 * A salted sha512 hash is used instead of bcrypt (not in mainline glibc)
	 *
	 * @param psw: password
	 *
	 * @author Ole Ernst
	 */
	public function psw_update($psw)
	{
		$salt = str_replace('+', '.', base64_encode(random_bytes(12)));
		$this->password = crypt($psw, sprintf('$6$%s$', $salt));
		$this->save();
	}

	/**
	 * Returns the type of an email address, which is derived from its index
	 *
	 * @author Ole Ernst
	 */
	public function get_type()
	{
		switch($this->index) {
			case 0:
				return trans('messages.disabled');
			case 1:
				return trans('messages.primary');
			default:
				return trans('messages.secondary');
		}
	}

}
