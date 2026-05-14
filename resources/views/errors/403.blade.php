@extends('errors::layout')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __('Access Denied'))
@section('description', __('You do not have the required permissions or role to access this workspace or section of the application.'))
