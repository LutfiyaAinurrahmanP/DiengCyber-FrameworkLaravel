<?php

namespace Lutfiyapr\KehadiranPegawai\Controllers;

use Lutfiyapr\KehadiranPegawai\Models\Presensi;
use Lutfiyapr\KehadiranPegawai\Models\Pegawai;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class PresensiController extends Controller
{
    /**
     * GET /api/presensi
     * Menampilkan semua data presensi
     */
    public function index(Request $request)
    {
        try {
            $query = Presensi::with('pegawai');

            // Filter berdasarkan tanggal
            if ($request->has('tanggal')) {
                $query->where('tanggal', $request->tanggal);
            }

            // Filter berdasarkan pegawai
            if ($request->has('pegawai_id')) {
                $query->where('pegawai_id', $request->pegawai_id);
            }

            // Filter berdasarkan bulan & tahun
            if ($request->has('bulan') && $request->has('tahun')) {
                $query->whereMonth('tanggal', $request->bulan)
                    ->whereYear('tanggal', $request->tahun);
            }

            $presensi = $query->orderBy('tanggal', 'desc')
                ->orderBy('jam_masuk', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil diambil',
                'data' => $presensi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data presensi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/presensi/{id}
     * Menampilkan detail presensi berdasarkan ID
     */
    public function show($id)
    {
        try {
            $presensi = Presensi::with('pegawai')->find($id);

            if (!$presensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Presensi tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data presensi berhasil diambil',
                'data' => $presensi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data presensi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/presensi/masuk
     * Presensi masuk (clock in)
     */
    public function masuk(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pegawai_id' => 'required|exists:lutfiyapr_kehadiranpegawai_pegawai,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah sudah presensi hari ini
            $today = Carbon::today()->toDateString();
            $existingPresensi = Presensi::where('pegawai_id', $request->pegawai_id)
                ->where('tanggal', $today)
                ->first();

            if ($existingPresensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi masuk hari ini',
                    'data' => $existingPresensi
                ], 400);
            }

            // Buat presensi baru
            $presensi = Presensi::create([
                'pegawai_id' => $request->pegawai_id,
                'tanggal' => $today,
                'jam_masuk' => Carbon::now()->format('H:i:s'),
                'status' => 'hadir',
                'keterangan' => $request->keterangan ?? null
            ]);

            $presensi->load('pegawai');

            return response()->json([
                'success' => true,
                'message' => 'Presensi masuk berhasil',
                'data' => $presensi
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan presensi masuk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/presensi/pulang
     * Presensi pulang (clock out)
     */
    public function pulang(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'pegawai_id' => 'required|exists:lutfiyapr_kehadiranpegawai_pegawai,id',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cek apakah sudah presensi masuk hari ini
            $today = Carbon::today()->toDateString();
            $presensi = Presensi::where('pegawai_id', $request->pegawai_id)
                ->where('tanggal', $today)
                ->first();

            if (!$presensi) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda belum melakukan presensi masuk hari ini'
                ], 400);
            }

            if ($presensi->jam_pulang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah melakukan presensi pulang hari ini',
                    'data' => $presensi
                ], 400);
            }

            // Update jam pulang
            $presensi->jam_pulang = Carbon::now()->format('H:i:s');
            if ($request->has('keterangan')) {
                $presensi->keterangan = $request->keterangan;
            }
            $presensi->save();

            $presensi->load('pegawai');

            return response()->json([
                'success' => true,
                'message' => 'Presensi pulang berhasil',
                'data' => $presensi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan presensi pulang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/presensi/pegawai/{pegawai_id}
     * Riwayat presensi berdasarkan pegawai
     */
    public function riwayatPegawai($pegawai_id)
    {
        try {
            $pegawai = Pegawai::find($pegawai_id);

            if (!$pegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ], 404);
            }

            $presensi = Presensi::where('pegawai_id', $pegawai_id)
                ->orderBy('tanggal', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Riwayat presensi berhasil diambil',
                'pegawai' => $pegawai,
                'data' => $presensi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat presensi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/presensi/status/{pegawai_id}
     * Cek status presensi hari ini
     */
    public function statusHariIni($pegawai_id)
    {
        try {
            $pegawai = Pegawai::find($pegawai_id);

            if (!$pegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ], 404);
            }

            $today = Carbon::today()->toDateString();
            $presensi = Presensi::where('pegawai_id', $pegawai_id)
                ->where('tanggal', $today)
                ->first();

            $status = [
                'sudah_masuk' => $presensi && $presensi->jam_masuk ? true : false,
                'sudah_pulang' => $presensi && $presensi->jam_pulang ? true : false,
                'data' => $presensi
            ];

            return response()->json([
                'success' => true,
                'message' => 'Status presensi hari ini',
                'pegawai' => $pegawai,
                'status' => $status
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil status presensi',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
