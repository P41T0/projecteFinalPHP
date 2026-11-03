@props(['code', 'title', 'message'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $code }} — {{ $title }}</title>
    @fluxAppearance
    <style>
        :root { color-scheme: light; font-family: ui-sans-serif, system-ui, sans-serif; }
        :root.dark { color-scheme: dark; }
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; padding: 1.5rem; background: #f4f7f4; color: #17231b; }
        main { width: min(100%, 34rem); padding: 3rem 2rem; text-align: center; background: #fff; border: 1px solid #dce5dc; border-radius: 1rem; box-shadow: 0 1.5rem 4rem #17331a12; }
        .code { margin: 0; color: #16803c; font-size: clamp(4rem, 18vw, 7rem); font-weight: 800; letter-spacing: -.08em; line-height: 1; }
        h1 { margin: 1.5rem 0 .75rem; font-size: 1.75rem; }
        .message { margin: 0 auto; max-width: 27rem; color: #526056; line-height: 1.65; }
        a { display: inline-flex; margin-top: 2rem; padding: .8rem 1.2rem; border-radius: .6rem; background: #16803c; color: #fff; font-weight: 600; text-decoration: none; }
        a:hover { background: #11652f; }
        a:focus-visible { outline: 3px solid #86efac; outline-offset: 3px; }
        :root.dark body { background: #09090b; color: #f4f4f5; }
        :root.dark main { background: #18181b; border-color: #3f3f46; box-shadow: 0 1.5rem 4rem #0005; }
        :root.dark .message { color: #a1a1aa; }
        :root.dark a { background: #4ade80; color: #052e16; }
        :root.dark a:hover { background: #86efac; }
    </style>
</head>
<body>
    <main>
        <p class="code">{{ $code }}</p>
        <h1>{{ $title }}</h1>
        <p class="message">{{ $message }}</p>
        <a href="{{ url('/') }}">{{ __("Tornar a l'inici") }}</a>
    </main>
</body>
</html>