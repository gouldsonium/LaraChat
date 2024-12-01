# LaraChat

LaraChat is Chat-GPT powered "chatbot" built on top of Laravel + Vue.js. Utilisiling [Jetstream](https://jetstream.laravel.com/introduction.html), the template has a built in authentication system and has Laravel setup with Vue being served with Inertia.js. 

- [Laravel](https://laravel.com) is accessible, powerful, and provides tools required for large, robust applications
- [Vue.js](https://vuejs.org) is an approachable, performant and versatile framework for building web user interfaces
- [Inertia.js](https://inertiajs.com) lets you build single-page apps, without building an API
- [Chat-GPT](https://chatgpt.com) is a generative AI chatbot

## Requirements 

- PHP V8.3
- cacert.pem
- Node.js V20
- Composer V2.7
- Open AI API key [found here](https://platform.openai.com/api-keys)

## Run Locally

To set up locally you need to run the following:

1. `composer install`
2. `npm install`
3. `cp .env.example .env`
4. Add your `OPEN_AI_KEY`
5. Run `php artisan serve`
6. In another terminal run `npm run dev`

## Deploy on Laravel Forge

**Coming soon**
