<?php

class PersonController extends BaseController {
    
    public static function login() {
        View::make('/suunnitelmat/login.html');
    }
    
    public static function handle_login() {
        $params = $_POST;
        
        $person = Person::authenticate($params['name'], $params['password']);
        
        if ($person == null) {
            View::make('/suunnitelmat/login.html', array('error' => 'Yritä uudelleen.',
                'name' => $params['name']));
        } else {
            $_SESSION['person'] = $person->id;
            
            Redirect::to('/', array('message' => 'Tervetuloa ' . $person->name . '!'));
        }
    }
    
    public static function logout() {
        $_SESSION['person'] = null;
        Redirect::to('/', array('message' => 'Olet kirjautunut ulos.'));
    }
    
    public static function register() {
        View::make('suunnitelmat/register.html');
    }
    
    public static function handle_register() {
        $params = $_POST;
        
        $attributes = array('name' => $params['name'], 'password' => $params['password']);
        $person = new Person($attributes);
        
        $errors = $person->errors();
        
        if (count($errors) > 0) {
            View::make('suunnitelmat/register.html', array('errors' => $errors, 'attributes' => $attributes));
            
        } else {
            $person->register();
            Redirect::to('/', array('message' => 'Sinut on rekisteröity. Ylläpitäjä antaa sinulle muokkausoikeudet piakkoin, ehkä.'));
        }
    }
    
    public static function settings() {
        self::check_logged_in();
        $persons = Person::all();
        View::make('suunnitelmat/settings.html', array('persons' => $persons));
    }
    
    public static function give_rights($id) {
        $person = Person::find($id);
        $person->give_rights();
        self::settings();
    }
    
    public static function take_rights($id) {
        $person = Person::find($id);
        $person->take_rights();
        self::settings();
    }
    
}
