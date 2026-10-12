@props(['phone'])
@php $url = \App\Services\PhoneService::whatsappUrl($phone); @endphp
@if($phone)
<span class="text-nowrap" dir="ltr">{{ $phone }}</span>
@if($url)<a href="{{ $url }}" target="_blank" rel="noopener" class="btn btn-sm btn-secondary py-0 px-2 ms-1" title="مراسلة عبر WhatsApp">WhatsApp</a>
@else<span class="badge badge-soft-off" title="حدّد رمز الدولة RIAYA_COUNTRY_CODE لتفعيل الرابط">؟</span>@endif
@else —@endif
