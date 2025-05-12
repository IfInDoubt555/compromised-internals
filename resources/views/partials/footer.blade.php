<footer class="bg-gray-100 dark:bg-gray-900 text-gray-700 dark:text-gray-300 py-10 border-t border-gray-300 dark:border-gray-700 mt-12">
    <div class="max-w-7xl mx-auto px-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

        <!-- Logo & Description -->
        <div>
            <h2 class="text-xl font-bold text-red-600 mb-2">Compromised Internals</h2>
            <p class="text-sm">Rally racing passion meets digital performance. Follow the history, culture, and chaos of rally worldwide.</p>
        </div>

        <!-- Support Links -->
        <div>
            <h3 class="text-md font-semibold mb-3">Support</h3>
            <ul class="space-y-2 text-sm">
                <li><a href="{{ route('contact') }}" class="hover:underline">Contact</a></li>
                <li><a href="{{ route('terms') }}" class="hover:underline">Terms of Service</a></li>
                <li><a href="{{ route('privacy') }}" class="hover:underline">Privacy Policy</a></li>
            </ul>
        </div>

        <!-- Social + Newsletter -->
        <div>
            <h3 class="text-md font-semibold mb-3">Stay Connected</h3>
            <div class="flex space-x-4 mb-4">
                <a href="#" class="hover:text-red-500"><i class="fab fa-twitter"></i></a>
                <a href="#" class="hover:text-red-500"><i class="fab fa-youtube"></i></a>
                <a href="#" class="hover:text-red-500"><i class="fab fa-instagram"></i></a>
            </div>
            <form class="text-sm">
                <label for="email" class="block mb-1">Join our newsletter:</label>
                <input type="email" id="email" placeholder="you@example.com" class="w-full px-3 py-2 rounded bg-white dark:bg-gray-800 border dark:border-gray-700 text-sm">
                <button type="submit" class="mt-2 w-full py-2 text-white bg-red-600 hover:bg-red-700 rounded text-sm">Subscribe</button>
            </form>
        </div>
    </div>

    <div class="text-center text-xs text-gray-500 dark:text-gray-500 mt-10 border-t border-gray-200 dark:border-gray-800 pt-6">
        &copy; {{ now()->year }} Compromised Internals. All rights reserved.
    </div>
</footer>