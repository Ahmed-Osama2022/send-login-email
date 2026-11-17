<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name') }} — Times</title>
  @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-stone-200 font-sans text-stone-800 antialiased selection:bg-amber-200/70">

  {{-- Blueprint grid backdrop --}}
  <div
       class="pointer-events-none fixed inset-0 bg-[radial-gradient(circle_at_1px_1px,#a8a29e_1px,transparent_0)] [background-size:24px_24px] opacity-40">
  </div>

  <main class="relative mx-auto w-full max-w-4xl px-4 py-8 sm:px-6 sm:py-14">
    {{-- Document sheet --}}
    <article
             class="relative overflow-hidden rounded-sm bg-white shadow-[0_1px_2px_rgba(28,25,23,0.06),0_18px_40px_-16px_rgba(28,25,23,0.35)] ring-1 ring-stone-900/10">

      {{-- Folded corner --}}
      <div
           class="pointer-events-none absolute right-0 top-0 h-16 w-16 bg-gradient-to-bl from-stone-300 via-stone-100 to-white shadow-[-1px_1px_2px_rgba(28,25,23,0.12)] [clip-path:polygon(100%_0,100%_100%,0_0)]">
      </div>

      {{-- Masthead --}}
      <header class="border-b border-stone-200 px-6 pb-8 pt-7 sm:px-12 sm:pt-10">
        <div
             class="flex flex-wrap items-center justify-between gap-3 text-[11px] font-semibold uppercase tracking-[0.22em] text-stone-500">
          <span>PC Opened Times</span>
          <span class="rounded-full border border-stone-300 px-2.5 py-1 text-stone-600">
            {{-- show the date here without time --}}
            {{ date('Y-m-d') }}
          </span>
        </div>

        <h1 class="mt-5 text-3xl font-semibold leading-tight tracking-tight text-stone-900 sm:text-4xl">
          All Times your PC was opened
        </h1>
        <p class="mt-3 max-w-xl text-[12px] leading-7 text-stone-600">
          This is a list of all the times your PC was opened. You can use this information to track your usage and see how often
          you use your PC.
          <br>
          <strong>this is relying on the cronjob runs on the machine </strong>
        </p>

        {{-- Document metadata, table-style like a real spec sheet --}}
        <dl
            class="mt-8 grid grid-cols-2 gap-px overflow-hidden rounded-sm border border-stone-200 bg-stone-200 text-sm sm:grid-cols-4">
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Records</dt>
            <dd class="mt-1 font-mono text-xs text-stone-800">{{ $times->count() }}</dd>
          </div>
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Latest Email</dt>
            <dd class="mt-1 truncate font-mono text-xs text-stone-800">{{ $latest?->email ?? '—' }}</dd>
          </div>
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Timezone</dt>
            <dd class="mt-1 font-mono text-xs text-stone-800">{{ $latest?->timezone ?? config('app.timezone') }}</dd>
          </div>
          <div class="bg-white px-4 py-3">
            <dt class="text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Latest Time</dt>
            <dd class="mt-1 font-mono text-xs text-stone-800">
              {{ $latest?->time?->format('Y-m-d H:i:s') ?? '—' }}
            </dd>
          </div>
        </dl>
      </header>

      <section class="px-6 py-8 sm:px-12 sm:py-10">
        <form method="GET" action="{{ url('/times') }}" class="mb-5 flex flex-wrap items-end justify-between gap-3">
          <div>
            <label for="sort" class="block text-[10px] font-semibold uppercase tracking-[0.18em] text-stone-500">Sort
              records</label>
            <select id="sort" name="sort"
                    class="mt-2 rounded-sm border border-stone-300 bg-white px-3 py-2 text-sm text-stone-700 outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500">
              <option value="latest" @selected($sort === 'latest')>Latest time first</option>
              <option value="oldest" @selected($sort === 'oldest')>Oldest time first</option>
              <option value="email_asc" @selected($sort === 'email_asc')>Email A–Z</option>
              <option value="email_desc" @selected($sort === 'email_desc')>Email Z–A</option>
              <option value="timezone_asc" @selected($sort === 'timezone_asc')>Timezone A–Z</option>
              <option value="timezone_desc" @selected($sort === 'timezone_desc')>Timezone Z–A</option>
            </select>
          </div>
          <button type="submit"
                  class="rounded-sm bg-stone-800 px-4 py-2 text-sm font-semibold text-white hover:bg-stone-700">Apply</button>
        </form>

        <div class="overflow-hidden rounded-sm border border-stone-200">
          <table class="w-full border-collapse text-left text-sm">
            <thead class="bg-stone-50 text-[10px] uppercase tracking-[0.18em] text-stone-500">
              <tr>
                <th class="border-b border-stone-200 px-4 py-3 font-semibold">Email</th>
                <th class="border-b border-stone-200 px-4 py-3 font-semibold">Time</th>
                <th class="border-b border-stone-200 px-4 py-3 font-semibold">Timezone</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-stone-200">
              @forelse ($times as $time)
                <tr class="bg-white hover:bg-stone-50">
                  <td class="px-4 py-3 font-mono text-xs text-stone-700">{{ $time->email }}</td>
                  <td class="px-4 py-3 font-mono text-xs text-stone-700">
                    {{ $time->time?->format('Y-m-d H:i:s') ?? '—' }}
                  </td>
                  <td class="px-4 py-3 font-mono text-xs text-stone-700">{{ $time->timezone }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="px-4 py-8 text-center text-stone-500">No recorded times yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>

    </article>
  </main>

</body>

</html>
