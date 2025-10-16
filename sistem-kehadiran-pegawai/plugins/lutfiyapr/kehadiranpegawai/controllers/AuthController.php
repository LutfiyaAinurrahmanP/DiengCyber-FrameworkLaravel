<?php namespace Lutfiyapr\KehadiranPegawai\Controllers;

use Lutfiyapr\KehadiranPegawai\Models\User;
use Lutfiyapr\KehadiranPegawai\Models\Pegawai;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Lutfiyapr\KehadiranPegawai\Models\Users;

class AuthController extends Controller
{
    /**
     * POST /api/auth/register
     * Register user baru
     */
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|unique:lutfiyapr_kehadiranpegawai_users,username',
                'email' => 'required|email|unique:lutfiyapr_kehadiranpegawai_users,email',
                'password' => 'required|min:6|confirmed',
                'role' => 'nullable|in:admin,pegawai',

                // Data Pegawai (optional, jika role = pegawai)
                'nip' => 'required_if:role,pegawai|unique:lutfiyapr_kehadiranpegawai_pegawai,nip',
                'nama' => 'required_if:role,pegawai|max:100',
                'jabatan' => 'required_if:role,pegawai|max:100',
                'no_telp' => 'nullable|max:20',
                'alamat' => 'nullable'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Jika role pegawai, buat data pegawai dulu
            $pegawai_id = null;
            if ($request->role === 'pegawai' || !$request->has('role')) {
                $pegawai = Pegawai::create([
                    'nip' => $request->nip,
                    'nama' => $request->nama,
                    'jabatan' => $request->jabatan,
                    'email' => $request->email,
                    'no_telp' => $request->no_telp,
                    'alamat' => $request->alamat,
                    'status' => 'aktif'
                ]);
                $pegawai_id = $pegawai->id;
            }

            // Buat user
            $user = new Users();
            $user->pegawai_id = $pegawai_id;
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = $request->password; // Will be hashed in beforeCreate
            $user->role = $request->role ?? 'pegawai';
            $user->save();

            // Generate token
            $token = $user->generateToken();

            // Load relasi pegawai
            $user->load('pegawai');

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registrasi gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/auth/login
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'login' => 'required', // bisa username atau email
                'password' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cari user berdasarkan username atau email
            $user = Users::where('username', $request->login)
                        ->orWhere('email', $request->login)
                        ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username atau email tidak ditemukan'
                ], 404);
            }

            // Verify password
            if (!$user->verifyPassword($request->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah'
                ], 401);
            }

            // Generate new token
            $token = $user->generateToken();

            // Update last login
            $user->updateLastLogin();

            // Load relasi pegawai
            $user->load('pegawai');

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'user' => $user,
                    'token' => $token
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * POST /api/auth/logout
     * Logout user
     */
    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
            }

            $user = Users::where('api_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            // Hapus token
            $user->api_token = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Logout berhasil'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Logout gagal',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * GET /api/auth/me
     * Get user yang sedang login
     */
    public function me(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
            }

            $user = Users::with('pegawai')->where('api_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data user berhasil diambil',
                'data' => $user
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * PUT /api/auth/change-password
     * Ubah password
     */
    public function changePassword(Request $request)
    {
        try {
            $token = $request->bearerToken();

            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token tidak ditemukan'
                ], 401);
            }

            $user = Users::where('api_token', $token)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak ditemukan'
                ], 404);
            }

            $validator = Validator::make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|min:6|confirmed'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Verify old password
            if (!$user->verifyPassword($request->old_password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password lama salah'
                ], 401);
            }

            // Update password
            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Password berhasil diubah'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah password',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
