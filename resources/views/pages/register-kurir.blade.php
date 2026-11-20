@extends('layouts.app')
@section('title', 'Daftar Kurir - SIFANTAR')
@php $hideNav = true; @endphp

@section('content')
    {{-- Halaman khusus pendaftaran mitra kurir: markup sama persis dengan pages.register,
         hanya tipe akun dikunci ke 'kurir' lewat $forceMode. --}}
    @include('pages.register', ['forceMode' => 'kurir'])
@endsection
