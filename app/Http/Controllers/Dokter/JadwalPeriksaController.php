<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JadwalPeriksa;
use Illuminate\Support\Facades\Auth;

class JadwalPeriksaController extends Controller
{
    public function index()
    {
        // Pastikan field id_dokter sesuai dengan struktur database Anda.
        // Jika id dokter ada di tabel terpisah (berelasi dengan user), gunakan: Auth::user()->dokter->id
        $jadwals = JadwalPeriksa::where('id_dokter', Auth::id())->get();
        return view('dokter.jadwal-periksa.index', compact('jadwals'));
    }

    public function create()
    {
        return view('dokter.jadwal-periksa.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $validated['id_dokter'] = Auth::id(); // Mengaitkan jadwal dengan dokter yang login
        $validated['status'] = 'Aktif'; // Set default status

        JadwalPeriksa::create($validated);
        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        $jadwal = JadwalPeriksa::where('id', $id)->where('id_dokter', Auth::id())->firstOrFail();
        return view('dokter.jadwal-periksa.edit', compact('jadwal'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'hari' => 'required|string',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'status' => 'required|in:Aktif,Tidak Aktif'
        ]);

        $jadwal = JadwalPeriksa::where('id', $id)->where('id_dokter', Auth::id())->firstOrFail();
        $jadwal->update($validated);
        
        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $jadwal = JadwalPeriksa::where('id', $id)->where('id_dokter', Auth::id())->firstOrFail();
        $jadwal->delete();
        
        return redirect()->route('jadwal-periksa.index')->with('success', 'Jadwal periksa berhasil dihapus!');
    }
}