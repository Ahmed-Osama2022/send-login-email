<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name') }} — Delivery Document</title>
  @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-stone-200 font-sans text-stone-800 antialiased selection:bg-amber-200/70">

  {{-- Blueprint grid backdrop --}}
  <div class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_1px_1px,#a8a29e_1px,transparent_0)] [background-size:24px_24px] opacity-40"></div>

  <main class="relative mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 sm:py-14">

    {{-- Document sheet --}}
    <article class="relative overflow-hidden rounded-sm bg-white shadow-[0_1px_2px_rgba(28,25,23,0.06),0_18px_40px_-16px_rgba(28,25,23,0.35)] ring-1 ring-stone-900/10">

      {{-- Folded corner --}}
      <div class="pointer-events-none absolute right-0 top-0 h-16 w-16 bg-gradient-to-bl from-stone-300 via-stone-100 to-white shadow-[-1px_1px_2px_rgba(28,25,23,0.12)] [clip-path:polygon(100%_0,100%_100%,0_0)]"></div>

      {{-- Masthead --}}
      <header class="border-b border-stone-200 px-6 pb-8 pt-7 sm:px-12 sm:pt-10">
        <div class="flex flex-wrap items-center justify-between gap-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-stone-500">
          <span>Operations document</span>
          <span class="rounded-full border border-stone-300 px-2.5 py-1 text-stone-600">Rev. 1.0</span>
        </div>

        <h1 class="mt-5 text-3xl font-semibold leading-tight tracking-tight text-stone-900 sm:text-4xl">
          Current Time Mailer
        </h1>
        <p class="mt-3 max-w-xl text-[15px] leading-7 text-stone-600">
          A document describing how {{ config('app.name') }} records the moment of a send and
          delivers it to an inbox — from the console command to the queued job.
        </p>

        {{-- Document metadata, table-style like a real spec sheet --}}
        <dl class="mt-8 grid grid-cols-2 gap-px overflow-hidden rounded-sm border border-stone-200 bg-stone-200 text-sm sm:grid-cols-4">
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Reference</dt>
            <dd class="mt-1 font-mono text-xs text-stone-800">CTM-001</dd>
          </div>
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Environment</dt>
            <dd class="mt-1 font-mono text-xs text-stone-800">{{ app()->environment() }}</dd>
          </div>
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Timezone</dt>
            <dd class="mt-1 font-mono text-xs text-stone-800">{{ config('app.timezone') }}</dd>
          </div>
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Issued</dt>
            <dd class="mt-1 font-mono text-xs text-stone-800">{{ now()->format('Y-m-d H:i') }}</dd>
          </div>
        </dl>
      </header>

      <div class="px-6 sm:px-12">

        {{-- 01 --}}
        <section class="grid gap-6 border-b border-stone-200 py-8 sm:grid-cols-[3rem_1fr] sm:py-10">
          <div class="hidden text-3xl font-semibold leading-none text-stone-300 sm:block">01</div>
          <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-900">Purpose</h2>
            <p class="mt-3 text-[15px] leading-7 text-stone-600">
              Every send is timestamped <em class="not-italic font-medium text-stone-800">before</em> the message leaves
              the application, so the record in the database and the time printed in the email can never
              disagree. A single <code class="rounded bg-stone-100 px-1.5 py-0.5 font-mono text-[13px] text-stone-800">now()</code>
              is captured by the job and reused for both.
            </p>

            <div class="mt-5 flex flex-wrap gap-2 text-xs font-medium">
              <span class="rounded-full bg-stone-900 px-3 py-1 text-white">mailer: {{ config('mail.default') }}</span>
              <span class="rounded-full bg-amber-100 px-3 py-1 text-amber-900 ring-1 ring-inset ring-amber-200">queue: {{ config('queue.default') }}</span>
              <span class="rounded-full bg-stone-100 px-3 py-1 text-stone-700 ring-1 ring-inset ring-stone-200">Laravel {{ app()->version() }}</span>
            </div>
          </div>
        </section>

        {{-- 02 --}}
        <section class="grid gap-6 border-b border-stone-200 py-8 sm:grid-cols-[3rem_1fr] sm:py-10">
          <div class="hidden text-3xl font-semibold leading-none text-stone-300 sm:block">02</div>
          <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-900">Delivery pipeline</h2>

            <ol class="mt-5 grid gap-3 sm:grid-cols-3 sm:gap-2">
              <li class="relative rounded-sm border border-stone-200 bg-stone-50 p-4">
                <span class="font-mono text-[11px] font-semibold text-amber-700">STEP 1</span>
                <p class="mt-2 text-sm font-semibold text-stone-900">Validate &amp; dispatch</p>
                <p class="mt-1 text-[13px] leading-6 text-stone-600">Console command checks the address, then pushes a job.</p>
                <span class="absolute -right-2 top-1/2 hidden h-2 w-2 rotate-45 border-r border-t border-stone-300 bg-stone-50 sm:block"></span>
              </li>
              <li class="relative rounded-sm border border-stone-200 bg-stone-50 p-4">
                <span class="font-mono text-[11px] font-semibold text-amber-700">STEP 2</span>
                <p class="mt-2 text-sm font-semibold text-stone-900">Capture &amp; record</p>
                <p class="mt-1 text-[13px] leading-6 text-stone-600">Worker stamps the time and writes a row to <span class="font-mono text-xs">times</span>.</p>
                <span class="absolute -right-2 top-1/2 hidden h-2 w-2 rotate-45 border-r border-t border-stone-300 bg-stone-50 sm:block"></span>
              </li>
              <li class="rounded-sm border border-stone-900/10 bg-amber-50 p-4 ring-1 ring-inset ring-amber-200/60">
                <span class="font-mono text-[11px] font-semibold text-amber-700">STEP 3</span>
                <p class="mt-2 text-sm font-semibold text-stone-900">Render &amp; send</p>
                <p class="mt-1 text-[13px] leading-6 text-stone-600">The same instant is rendered into the Blade mailable.</p>
              </li>
            </ol>
          </div>
        </section>

        {{-- 03 --}}
        <section class="grid gap-6 border-b border-stone-200 py-8 sm:grid-cols-[3rem_1fr] sm:py-10">
          <div class="hidden text-3xl font-semibold leading-none text-stone-300 sm:block">03</div>
          <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-900">Command reference</h2>

            <div class="mt-4 overflow-hidden rounded-sm bg-stone-900 shadow-inner">
              <div class="flex items-center gap-1.5 border-b border-white/10 px-4 py-2.5">
                <span class="h-2 w-2 rounded-full bg-stone-600"></span>
                <span class="h-2 w-2 rounded-full bg-stone-600"></span>
                <span class="h-2 w-2 rounded-full bg-amber-500/80"></span>
                <span class="ml-2 font-mono text-[11px] text-stone-400">~/{{ basename(base_path()) }}</span>
              </div>
              <pre class="overflow-x-auto px-4 py-4 font-mono text-[12.5px] leading-7 text-stone-300"><code><span class="text-stone-500"># queue a send (worker required)</span>
<span class="text-amber-300">php</span> artisan mail:send-current-time you@example.com

<span class="text-stone-500"># send immediately, no worker</span>
<span class="text-amber-300">php</span> artisan mail:send-current-time you@example.com <span class="text-emerald-300">--sync</span>

<span class="text-stone-500"># keep the queue alive</span>
<span class="text-amber-300">php</span> artisan queue:work</code></pre>
            </div>

            <p class="mt-3 text-[13px] leading-6 text-stone-500">
              Add <code class="font-mono text-xs text-stone-700">--name="…"</code> to personalise the greeting.
            </p>
          </div>
        </section>

        {{-- 04 --}}
        <section class="grid gap-6 py-8 sm:grid-cols-[3rem_1fr] sm:py-10">
          <div class="hidden text-3xl font-semibold leading-none text-stone-300 sm:block">04</div>
          <div>
            <h2 class="text-sm font-semibold uppercase tracking-[0.18em] text-stone-900">Record schema</h2>
            <p class="mt-2 text-[13px] leading-6 text-stone-500">Table <span class="font-mono text-stone-700">times</span> — one row per send attempt.</p>

            <div class="mt-4 overflow-x-auto rounded-sm border border-stone-200">
              <table class="w-full border-collapse text-left text-sm">
                <thead class="bg-stone-50 text-[11px] uppercase tracking-[0.16em] text-stone-500">
                  <tr>
                    <th scope="col" class="border-b border-stone-200 px-4 py-2.5 font-semibold">Column</th>
                    <th scope="col" class="border-b border-stone-200 px-4 py-2.5 font-semibold">Type</th>
                    <th scope="col" class="border-b border-stone-200 px-4 py-2.5 font-semibold">Notes</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-stone-100 font-mono text-[12.5px] text-stone-700">
                  <tr>
                    <td class="px-4 py-2.5">id</td>
                    <td class="px-4 py-2.5 text-stone-500">bigint</td>
                    <td class="px-4 py-2.5 font-sans text-[13px] text-stone-500">Primary key</td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2.5">email</td>
                    <td class="px-4 py-2.5 text-stone-500">varchar</td>
                    <td class="px-4 py-2.5 font-sans text-[13px] text-stone-500">Indexed recipient</td>
                  </tr>
                  <tr class="bg-amber-50/60">
                    <td class="px-4 py-2.5">time</td>
                    <td class="px-4 py-2.5 text-stone-500">timestamp</td>
                    <td class="px-4 py-2.5 font-sans text-[13px] text-stone-500">Moment captured before sending</td>
                  </tr>
                  <tr>
                    <td class="px-4 py-2.5">timezone</td>
                    <td class="px-4 py-2.5 text-stone-500">varchar(64)</td>
                    <td class="px-4 py-2.5 font-sans text-[13px] text-stone-500">Zone name, e.g. Africa/Cairo</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>

      {{-- Sign-off --}}
      <footer class="border-t border-stone-200 bg-stone-50/70 px-6 py-6 sm:px-12">
        <div class="flex flex-wrap items-end justify-between gap-6">
          <div>
            <div class="h-8 w-40 border-b border-dashed border-stone-400"></div>
            <p class="mt-2 text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Prepared by</p>
          </div>

          <div class="flex items-center gap-4">
            <div class="rotate-[-6deg] rounded-sm border-2 border-stone-400/70 px-3 py-1 font-mono text-[10px] font-bold uppercase tracking-[0.2em] text-stone-500">
              queued
            </div>
            <p class="text-right font-mono text-[11px] leading-5 text-stone-500">
              {{ config('app.name') }}<br>
              {{ now()->toDayDateTimeString() }}
            </p>
          </div>
        </div>

        <p class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-stone-400">
          <span>Page 1 of 1</span>
          <span aria-hidden="true">·</span>
          <span>PHP {{ PHP_VERSION }}</span>
          <span aria-hidden="true">·</span>
          <span>Tailwind CSS 3</span>
        </p>
      </footer>
    </article>

    <p class="mt-6 text-center font-mono text-[11px] uppercase tracking-[0.2em] text-stone-400">
      Document ends
    </p>
  </main>
</body>
</html>
