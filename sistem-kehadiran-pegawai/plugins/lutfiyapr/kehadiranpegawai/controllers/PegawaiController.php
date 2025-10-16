<?php

namespace Lutfiyapr\KehadiranPegawai\Controllers;

use Lutfiyapr\KehadiranPegawai\Models\Pegawai;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PegawaiController extends Controller
{
    /**
     * GET /api/pegawai
     * Menampilkan semua data pegawai
     */
    public function index()
    {
        try {
            $pegawai = Pegawai::orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pegawai berhasil diambil',
                'data' => $pegawai
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/pegawai/{id}
     * Menampilkan detail pegawai berdasarkan ID
     */
    public function show($id)
    {
        try {
            $pegawai = Pegawai::with('presensi')->find($id);

            if (!$pegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data pegawai berhasil diambil',
                'data' => $pegawai
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/pegawai
     * Membuat data pegawai baru
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nip' => 'required|unique:lutfiyapr_kehadiranpegawai_pegawai,nip',
                'nama' => 'required|max:100',
                'jabatan' => 'required|max:100',
                'email' => 'required|email|unique:lutfiyapr_kehadiranpegawai_pegawai,email',
                'no_telp' => 'nullable|max:20',
                'alamat' => 'nullable',
                'status' => 'nullable|in:aktif,non-aktif'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pegawai = Pegawai::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil ditambahkan',
                'data' => $pegawai
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/pegawai/{id}
     * Update data pegawai
     */
    public function update(Request $request, $id)
    {
        try {
            $pegawai = Pegawai::find($id);

            if (!$pegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'nip' => 'required|unique:lutfiyapr_kehadiranpegawai_pegawai,nip,' . $id,
                'nama' => 'required|max:100',
                'jabatan' => 'required|max:100',
                'email' => 'required|email|unique:lutfiyapr_kehadiranpegawai_pegawai,email,' . $id,
                'no_telp' => 'nullable|max:20',
                'alamat' => 'nullable',
                'status' => 'nullable|in:aktif,non-aktif'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pegawai->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil diupdate',
                'data' => $pegawai
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DELETE /api/pegawai/{id}
     * Hapus data pegawai
     */
    public function destroy($id)
    {
        try {
            $pegawai = Pegawai::find($id);

            if (!$pegawai) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pegawai tidak ditemukan'
                ], 404);
            }

            $pegawai->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pegawai berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus pegawai',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
