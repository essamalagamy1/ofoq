<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="scroll-smooth light" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
    data-theme="light">
@include('partials.head')

<body
    class="flex flex-col min-h-screen antialiased dark:bg-linear-to-b font-sans text-gray-900 dark:text-gray-100 transition-colors duration-300 relative">
    {{-- <div class="absolute top-4 right-4 left-4 z-50 flex items-center gap-2 ">
	<div class="dropdown dropdown-start">
		<div tabindex="0" role="button" class="flex items-center justify-center p-1.5 sm:p-2 rounded-full text-gray-700 dark:text-gray-300  dark:bg-gray-900 transition-colors duration-200">
			<x-icon name="o-language" class="w-6 h-6 sm:w-8 sm:h-8" />
		</div>
		<ul tabindex="0" class="dropdown-content menu mt-2 dark:bg-gray-900 rounded-lg z-50 w-max min-w-[10rem] p-2 shadow-md bg-white border border-gray-100 dark:border-gray-800 origin-top">
			<li>
				<a class="flex items-center w-full px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors duration-200 text-base font-bold {{app()->getLocale() === 'en' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300'}}"
				   href="{{route('web-language','en')}}">
					<x-flag-country-us class="w-[20px] h-auto me-3"/>
					<span>English</span>
				</a>
			</li>
			<li>
				<a class="flex items-center w-full px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-md transition-colors duration-200 text-base font-bold {{app()->getLocale() === 'ar' ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-700 dark:text-gray-300'}}"
				   href="{{route('web-language','ar')}}">
					<x-flag-country-eg class="w-[20px] h-auto me-3"/>
					<span>العربية</span>
				</a>
			</li>
		</ul>
	</div>
</div> --}}
    <div style="background-color: #0b1c38;"
        class="w-full flex items-center justify-between px-6 md:px-12 py-3 md:py-4 shadow-lg border-b-2 border-[#d4a85a]/30 z-20 relative">
        <div style="color: #d4a85a;" class="font-bold text-sm md:text-xl drop-shadow-md">
            نكتب قصة نجاح
        </div>
        <div style="color: #d4a85a;" class="font-bold text-sm md:text-xl drop-shadow-md">
            الابتدائية الرابعة والتسعون
        </div>
    </div>
    <div class="bg-background flex-1 flex flex-col items-center justify-center gap-6 py-8 px-4 relative w-full"
        style="background-image: url('{{ asset('bg.png') }}'); background-size: cover; background-repeat: no-repeat; background-position: center;">

        <!-- Main Overlay Container -->
        <div class="flex flex-col gap-2 w-full max-w-xl z-10 relative mt-20 md:mt-16">

            <!-- 12 Weeks Badge (Floating) -->
            <div class="p-2 absolute -top-20 md:-top-20 -right-2 md:-right-20 rounded-full flex flex-col items-center justify-center shadow-2xl z-20 w-[110px] h-[110px] md:w-[150px] md:h-[150px]"
                style="transform: rotate(-10deg); background: linear-gradient(180deg, #0b1c38, #1e3a8a); border: 3px solid #d4a85a; color: white;">
                <x-icon name="o-clock" class="w-7 h-7 md:w-8 md:h-8 mb-1" style="color: #d4a85a;" />
                <span class="text-xs md:text-base font-bold">رحلة</span>
                <span class="text-3xl md:text-4xl font-black leading-none my-1" style="color: #d4a85a;">١٢</span>
                <span class="text-xs md:text-base font-bold">أسبوعاً</span>
            </div>

            <!-- Header Titles -->
            <div class="flex flex-col items-center text-center mb-2 md:mb-2">
                <h1 class="font-bold drop-shadow-xl"
                    style="font-size: clamp(60px, 8vw, 90px); -webkit-text-stroke: 2.5px #d4a85a; color: #0b1c38; line-height: 1;">
                    أفق</h1>
                <h2 class="font-extrabold mt-4"
                    style="font-size: clamp(20px, 4vw, 26px); color: #0b1c38; text-shadow: 0 2px 4px rgba(255,255,255,0.8);">
                    نحلق نحو التميز</h2>
                {{-- <div class="flex items-center gap-2 mt-2 font-bold"
                    style="font-size: clamp(16px, 3vw, 20px); color: #0b1c38; text-shadow: 0 2px 4px rgba(255,255,255,0.8);">
                    <span style="color: #d4a85a; font-size: 14px;">✦</span>
                    <span>نحلق نحو التميز</span>
                    <span style="color: #d4a85a; font-size: 14px;">✦</span>
                </div> --}}
            </div>

            <!-- Login Card Slot -->
            <div class="flex flex-col gap-6 w-full">
                {{ $slot }}
            </div>

            {{-- <!-- Bottom Banner -->
            <div class="mt-4 text-center pb-8">
                <div class="inline-flex items-center justify-center px-5 md:px-8 py-3 rounded-full font-bold text-sm md:text-lg shadow-xl w-full max-w-[95%]"
                    style="background-color: #51258c; border: 3px solid #d4a85a; color: white;">
                    <span class="mx-2 text-xl" style="color: #d4a85a;">✦</span>
                    اجتازي المحطات، اجمعي الأوسمة، واصلي التحليق
                    <span class="mx-2 text-xl" style="color: #d4a85a;">✦</span>
                </div>
            </div> --}}

        </div>
    </div>
    <div style="background-color: #0b1c38;"
        class="py-4 text-center border-t-2 border-[#d4a85a]/30 shadow-[0_-5px_15px_rgba(0,0,0,0.2)] z-20 relative w-full">
        <h3 class="text-xl md:text-3xl font-black drop-shadow-md flex items-center justify-center gap-3"
            style="color: #d4a85a;">
            من الفجوة إلى الأثر
            <img src="{{ asset('1111111.png') }}" class="h-10 md:h-10 w-auto object-contain" alt="1111111" />
        </h3>
    </div>
</body>

</html>
