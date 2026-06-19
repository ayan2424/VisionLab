@extends('errors::layout')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', __('Server malfunction'))
@section('description', __('Something went wrong on our end. Our AI agent has been dispatched to investigate the logs.'))


