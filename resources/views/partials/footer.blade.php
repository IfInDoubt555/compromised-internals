<footer class="bg-gray-900 text-white py-12">
    <div class="max-w-5xl mx-auto px-4 flex flex-col md:flex-row justify-center gap-x-16 gap-y-10 text-left">

        <!-- Column 1: Brand -->
        <div class="md:w-1/3">
            <h2 class="text-2xl font-bold text-red-600">Compromised Internals</h2>
            <p class="mt-2 text-sm text-gray-300">
                Rally racing passion meets digital performance. Follow the history, culture, and chaos of rally worldwide.
            </p>
        </div>

        <!-- Column 2: Support -->
        <div>
            <h3 class="font-semibold mb-2">Support</h3>
            <ul class="space-y-1 text-sm text-gray-300">
                <li>
                    <a href="{{ route('contact') }}" class="hover:underline">Contact</a>
                </li>
                <li>
                    <a href="{{ route('terms') }}" class="hover:underline">Terms of Service</a>
                </li>
                <li>
                    <a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a>
                </li>
                <li>
                    <a href=" {{ route('security.policy') }}" class="hover:underline">Security Disclosure</a>
                </li>

            </ul>
        </div>

        <!-- Column 3: Newsletter -->
        <div>
            <h3 class="font-semibold mb-2">Stay Connected</h3>
            <p class="text-sm text-gray-300 mb-2">Join our newsletter:</p>
            <form class="flex flex-col gap-2">
                <input type="email" placeholder="you@example.com"
                    class="px-4 py-2 rounded bg-gray-800 text-white placeholder-gray-400 border border-gray-700" />
                <button type="submit"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded">
                    Subscribe
                </button>
            </form>
        </div>

    </div>

    <div class="mt-8 text-center text-xs text-gray-500">
        © 2025 Compromised Internals. All rights reserved.
    </div>
</footer>