<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    public function showStep1() {
        return view('initial_information');
    }

    public function handleStep1(Request $request) {
        session()->put('trip.step1', $request->only(['destination', 'adults', 'children', 'departure_date', 'return_date']));
        return redirect()->route('trip.form.step2.view');
    }

    public function showStep2() {
        $transportes = ['Carro', 'Ônibus', 'Avião', 'Trem'];
        $estadias = ['Hotel', 'Hostel', 'Pousada', 'Airbnb', 'Camping'];

        return view('trip_details', compact('transportes', 'estadias'));
    }

    public function handleStep2(Request $request) {
        session()->put('trip.step2', $request->only(['transportation', 'accommodation']));
        return redirect()->route('trip.form.step3.view');
    }

    public function showStep3() {
        return view('trip_preferences');
    }

    public function handleStep3(Request $request) {
        session()->put('trip.step3', ['preference' => $request->input('preference')]);
        return redirect()->route('trip.form.step4.view');
    }

    public function showStep4()
{
    $scriptPath = base_path('scripts/webscraping/scraping.py');
    $command = 'python "' . $scriptPath . '" 2>&1';
    $output = shell_exec($command);
    $frases = $output ? explode("\n", trim($output)) : ['Erro ao executar ou sem dados.'];

    // Apenas para teste, pode deixar o $description vazio se quiser
    $description = implode("\n\n", $frases); // ou substitua com outro texto
    return view('trip_insurance', compact('frases', 'description'));
}



    public function handleStep4(Request $request) {
        session()->put('trip.step4', $request->only(['budget', 'insurance_option']));
        return redirect()->route('trip.form.step5.view');
    }

    public function showStep5() {
        return view('trip_flights');
    }

    public function handleStep5(Request $request) {
        session()->put('trip.step5', ['flight_option' => $request->input('flight_option')]);
        return redirect()->route('trip.form.step6.view');
    }

    public function showStep6() {
        $trip = array_merge(
            session('trip.step1', []),
            session('trip.step2', []),
            session('trip.step3', []),
            session('trip.step4', []),
            session('trip.step5', [])
        );
        return view('trip_review', compact('trip'));
    }

    public function finish(Request $request) {
        // Aqui você pode salvar tudo no banco de dados
        session()->forget('trip');
        return redirect()->route('explore'); // Ajuste conforme rota real
    }
}