<?php

namespace App;

use Silber\Bouncer\Database\Concerns\IsRole;

class Role extends BaseModel
{
    use IsRole;

    public $table = 'roles';

    protected static $undeletables = ['admin', 'support'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'title',
        'description',
        'rank',
    ];

    public static function rules($id = null)
    {
        return [
            'name' => 'required|unique:roles,name,'.$id.',id,deleted_at,NULL',
            'rank' => 'required|integer|max:101|min:0',
        ];
    }

    public static function view_headline() : string
    {
        return 'Roles';
    }

    // View Icon
    public static function view_icon() : string
    {
        return '<i class="fa fa-user-circle text-info"></i>';
    }

    // link title in index view
    public function view_index_label()
    {
        return [
            'table'			=> $this->table,
            'index_header'	=> [$this->table.'.title', $this->table.'.rank', $this->table.'.description'],
            'header'		=> $this->name,
            'order_by'		=> ['1' => 'desc'],
        ];
    }

    public function set_index_delete()
    {
        if (in_array($this->name, self::$undeletables)) {
            $this->index_delete_disabled = true;
        }
    }

    public function view_has_many()
    {
        $ret['Base']['Abilities']['view']['view'] = 'auth.abilities';

        return $ret;
    }
}
