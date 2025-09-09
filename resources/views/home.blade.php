@extends('layouts.app')

@section('title', "Welcome to Liam's Birthday Party!")

@section('content')
<!-- Language Switcher -->
<div class="fixed top-4 right-4 z-50">
    <form action="{{ route('switch-language') }}" method="POST" class="flex items-center space-x-2">
        @csrf
        <button type="submit" name="language" value="{{ app()->getLocale() == 'nl' ? 'en' : 'nl' }}" 
                class="bg-white bg-opacity-95 hover:bg-opacity-100 shadow-lg rounded-full px-3 py-2 md:px-4 md:py-2 flex items-center space-x-2 transition-all hover:scale-105 border border-gray-200">
            @if(app()->getLocale() == 'nl')
                <span class="text-lg md:text-xl">🇬🇧</span>
                <span class="font-medium text-gray-700 text-sm md:text-base">English</span>
            @else
                <span class="text-lg md:text-xl">🇳🇱</span>
                <span class="font-medium text-gray-700 text-sm md:text-base">Nederlands</span>
            @endif
        </button>
    </form>
</div>

<div class="container mx-auto px-4 py-16">
    <div class="text-center">
        <h1 class="text-6xl md:text-8xl fun-font text-purple-600 mb-4 bounce">
            {{ app()->getLocale() == 'nl' ? 'Liam wordt 5!' : 'Liam is Turning 5!' }}
        </h1>
        
        <div class="text-4xl mb-8">🎉 🎂 🎈 🎁 🦕</div>
        
        <div class="max-w-2xl mx-auto bg-white/90 rounded-3xl shadow-xl p-8 mb-8">
            <p class="text-2xl text-gray-700 mb-6">
                {{ app()->getLocale() == 'nl' ? 'Je bent uitgenodigd voor een geweldig Dinosaurus Avontuur!' : "You're invited to an amazing Dinosaur Adventure!" }}
            </p>
            
            <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white rounded-2xl p-1 mb-6">
                <div class="bg-white rounded-xl p-6 text-gray-800">
                    <p class="text-xl mb-2">{{ app()->getLocale() == 'nl' ? 'Heb je een persoonlijke uitnodigingslink ontvangen?' : 'If you received a personal invitation link,' }}</p>
                    <p class="text-lg">{{ app()->getLocale() == 'nl' ? 'Gebruik deze dan om naar je gepersonaliseerde pagina te gaan!' : 'please use that to access your personalized page!' }}</p>
                </div>
            </div>
            
            <div class="flex justify-center space-x-4">
                <a href="/guestbook" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-full transform hover:scale-105 transition-all">
                    {{ app()->getLocale() == 'nl' ? 'Gastenboek Bekijken' : 'View Guestbook' }}
                </a>
                <a href="/admin" class="bg-purple-500 hover:bg-purple-600 text-white font-bold py-3 px-6 rounded-full transform hover:scale-105 transition-all">
                    Admin
                </a>
            </div>
        </div>
        
        <div class="text-gray-600">
            <p>{{ app()->getLocale() == 'nl' ? 'Vragen? Neem contact op met de feestouders!' : 'Questions? Contact the party parents!' }}</p>
        </div>
    </div>
</div>

<!-- Floating elements -->
<div class="fixed top-10 left-10 text-6xl animate-pulse">🎈</div>
<div class="fixed top-20 right-20 text-5xl animate-pulse" style="animation-delay: 0.5s">🦕</div>
<div class="fixed bottom-20 left-20 text-5xl animate-pulse" style="animation-delay: 1s">🎁</div>
<div class="fixed bottom-10 right-10 text-6xl animate-pulse" style="animation-delay: 1.5s">🎂</div>
@endsection