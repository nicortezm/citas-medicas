<?php

namespace App\Http\Controllers;

use App\Exceptions\AppointmentException;
use App\Models\Appointment;
use App\Http\Requests\Appointment\StoreAppointmentRequest;
use App\Http\Requests\Appointment\UpdateAppointmentRequest;
use App\Services\AppointmentService;
use App\Services\MercadoPagoService;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
class AppointmentController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */

    public function __construct(
        private AppointmentService $appointmentService,
        private MercadoPagoService $mercadoPagoService,
    ) {
    }

    public function index()
    {
        ability:
        $this->authorize('viewAppointments', Appointment::class);

        $user = auth()->user(); // Obtiene el usuario autenticado

        // Filtra los appointments dependiendo del rol del usuario
        if ($user->role === 'admin') {
            return Appointment::all(); // Si es admin, muestra todos los appointments
        }
        if ($user->role === 'patient') {
            return Appointment::where('patient_id', $user->id)->get(); // Si es paciente, muestra sus appointments
        }
        if ($user->role === 'doctor') {
            return Appointment::where('doctor_id', $user->id)->get(); // Si es doctor, muestra los appointments del doctor
        }

        // Si no tiene el rol adecuado, deniega el acceso
        return response()->json([], 403); // Acceso denegado
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAppointmentRequest $request)
    {
        $data = $request->validated();
        try {
            $appointment = $this->appointmentService->createAppointment($data, $request->user());
            return response()->json([
                'appointment' => $appointment,
            ], 201);
        } catch (AppointmentException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        $this->authorize('view', $appointment);
        return response()->json($appointment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment)
    {
        // $this->authorize('view', $appointment);
        // $data = $request->validated();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
        //
    }

    public function confirmAppointment(Request $request)
    {
        $appointment = $this->appointmentService->getAppointment($request->input('appointment_id') ?? 0); // cero indica que no existe el registro
        if (!$appointment) {
            return response()->json(['message' => 'Error'], 400);
        }
        $this->authorize('confirm', $appointment);
        try {
            $this->appointmentService->confirmAppointment($appointment);
            return response()->json(['message' => 'Appointment confirmed'], 200);
        } catch (AppointmentException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }
    }

    public function viewTodayAppointments(Request $request)
    {
        $this->authorize('viewAppointments', Appointment::class);
        $user = auth()->user();
        if ($user->role === 'doctor') {
            return Appointment::where('doctor_id', $user->id)
                ->whereDate('date', Carbon::today()->addDays(1)->toDateString()) // Comparar solo la parte de la fecha
                ->get();
        }
        return 'no permitido';
    }


}
