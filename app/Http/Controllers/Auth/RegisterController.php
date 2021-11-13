<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    //protected $redirectTo = RouteServiceProvider::HOME;
    protected function redirectTo()
    {
        return '/attendee';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'mobile_number' => ['required', 'digits:11', 'regex:/(09)[0-9]{9}/', 'numeric', 'unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'code' => ['nullable', 'exists:events'],
            'as' => ['required', Rule::in(['attendee', 'organizer'])],

            'organization_name' => ['required_if:as,organizer'],
            'department' => ['required_if:as,organizer'],

            'attendee_organization_name' => ['required_if:as,attendee'],
            'attendee_occupation' => ['required_if:as,attendee']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'mobile_number' => $data['mobile_number'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $event = false;

        if(isset($data['code'])) { //if a code(event) is specified, check its validity
            try {
              $event = Event::whereCode($data['code'])->firstOrFail();
            } catch (ModelNotFoundException $e) {
              return abort(404); //TODO: specif why an error occured to the user
            }
        }

        if($data['as'] === 'attendee') {
            $user->attendee_organization_name = $data['attendee_organization_name'];
            $user->attendee_occupation = $data['attendee_occupation'];
            $user->save();
        }

        if($event && $data['as'] === 'attendee') {
            $event->attendees()->attach($user->id, [
                'is_confirmed'=> 1
            ]);
        }

        if($data['as'] === 'organizer') {
            $user->organization()->create([
                'name' => $data['organization_name'],
                'department' => $data['department']
            ]);

            $user->evaluations()->create([
                'name' => 'Default Evaluation Sheet',
                'description' => 'The default evaluation sheet for organizations',
                'questions' => null
            ]);

            $event_folder_path = "storage/users/organizers/$user->id/temp_docs"; // temp docs for uploading event files
            File::makeDirectory($event_folder_path, 0777, true);
        }

        $user->assignRole($data['as']);

        return $user;
    }

      /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = $this->create($request->all());

        if(! $request->has('code')) {
            event(new Registered($user));
        }

        $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        if($request->has('code')) {
            $user->email_verified_at = Carbon::now();
            $user->save();

            return redirect()->route('events.show', [$request->code])->with('message', 'Successfuly confirmed invitation');
        }
    }
}
