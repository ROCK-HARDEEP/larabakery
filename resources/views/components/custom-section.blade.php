@props(['section'])

@php
    $layout = $section->layout ?? 'default';
    $componentPath = "custom-sections.{$layout}";
@endphp

@if(View::exists("components.{$componentPath}"))
    @include("components.{$componentPath}", ['section' => $section])
@else
    @include('components.custom-sections.default', ['section' => $section])
@endif