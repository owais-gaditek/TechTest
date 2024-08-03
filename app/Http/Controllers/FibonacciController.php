<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FibonacciController extends Controller
{
    /**
     * Calculate Fibonacci view.
     */
    public function index()
    {
        return view('fibonacci/fibonacci');
    }

    /**
     * Calculate Fibonacci sequence up to the given number.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculate(Request $request)
    {
        $request->validate([
            'n' => 'required|integer|min:1',
        ]);

        $n = (int) $request->input('n');
        $sequence = $this->fibonacci($n);

        return response()->json([
            'success' => true,
            'sequence' => $sequence,
        ]);
    }

    /**
     * Generate Fibonacci sequence up to n.
     *
     * @param int $n
     * @return array
     */
    private function fibonacci($n)
    {
        $sequence = [0, 1];
        while (count($sequence) <= $n) {
            $sequence[] = $sequence[count($sequence) - 1] + $sequence[count($sequence) - 2];
        }
        return array_slice($sequence, 0, $n);
    }
}
