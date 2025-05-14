<x-mail::message>
# Introduction

<p>Your OTP is {{ $otp }}</p>

<p>Use this OTP to verify your email address.</p>
<p>Note: This OTP is valid for 5 minutes.</p>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
