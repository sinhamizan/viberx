import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useMemo, useState } from 'react';

export default function Show({ states, resumeRoute }) {
  const [query, setQuery] = useState('');
  const [isOpen, setIsOpen] = useState(false);

  const { data, setData, post, processing, errors } = useForm({
    state: '',
  });

  const selected = states.find((state) => state.code === data.state);

  const filtered = useMemo(() => {
    if (!query) {
      return states;
    }

    const needle = query.toLowerCase();

    return states.filter((state) => state.name.toLowerCase().includes(needle));
  }, [query, states]);

  const selectState = (state) => {
    setData('state', state.code);
    setQuery(state.name);
    setIsOpen(false);
  };

  const changeState = () => {
    setData('state', '');
    setQuery('');
    setIsOpen(true);
  };

  const submit = (e) => {
    e.preventDefault();
    post(route('state.store'));
  };

  return (
    <GuestLayout>
      <Head title="Confirm your state" />

      <span className="mx-auto block w-fit rounded border border-neutral-700 px-2 py-0.5 text-xs font-semibold uppercase tracking-wide text-neutral-400">
        Start your private assessment
      </span>

      <h1 className="mt-4 text-center text-2xl font-bold text-white">
        First, confirm your state.
      </h1>
      <p className="mt-2 text-center text-sm text-neutral-400">
        Treatment availability depends on where care will be received. Select
        your state before entering medical or payment information.
      </p>

      <form onSubmit={submit} className="mt-8">
        <label className="block text-xs font-medium uppercase tracking-wide text-neutral-400">
          State of treatment
        </label>

        <div className="relative mt-1 flex gap-2">
          <div className="relative flex-1">
            <input
              value={query}
              placeholder="Search your state"
              disabled={!!selected}
              onFocus={() => setIsOpen(true)}
              onChange={(e) => {
                setQuery(e.target.value);
                setIsOpen(true);
              }}
              className={
                'block w-full rounded-md border shadow-sm ' +
                (selected && !selected.available
                  ? 'border-red-500 bg-neutral-800 text-red-400'
                  : 'border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 focus:border-emerald-400 focus:ring-emerald-400')
              }
            />

            {selected && !selected.available && (
              <span className="absolute right-3 top-1/2 -translate-y-1/2 text-xs font-semibold text-red-400">
                (Unavailable)
              </span>
            )}

            {isOpen && !selected && (
              <ul className="absolute z-10 mt-1 max-h-56 w-full overflow-y-auto rounded-md border border-neutral-700 bg-neutral-800 shadow-lg">
                {filtered.map((state) => (
                  <li key={state.code}>
                    <button
                      type="button"
                      onClick={() => selectState(state)}
                      className="block w-full px-3 py-2 text-left text-sm text-neutral-200 hover:bg-neutral-700"
                    >
                      {state.name}
                    </button>
                  </li>
                ))}
                {filtered.length === 0 && (
                  <li className="px-3 py-2 text-sm text-neutral-500">
                    No matching state
                  </li>
                )}
              </ul>
            )}
          </div>

          {selected ? (
            <button
              type="button"
              onClick={changeState}
              className="shrink-0 rounded-md border border-neutral-700 px-4 text-xs font-semibold uppercase tracking-wide text-neutral-300"
            >
              Change State
            </button>
          ) : (
            <button
              type="submit"
              disabled={processing || !data.state}
              className="shrink-0 rounded-md bg-emerald-300 px-4 text-xs font-semibold uppercase tracking-wide text-neutral-900 hover:bg-emerald-200 disabled:opacity-40"
            >
              Confirm State
            </button>
          )}
        </div>

        {selected && selected.available && (
          <button
            type="submit"
            disabled={processing}
            className="mt-4 w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
          >
            Confirm State
          </button>
        )}

        <InputError message={errors.state} className="mt-2" />

        {resumeRoute && (
          <p className="mt-4 text-sm text-neutral-400">
            Already started an assessment?{' '}
            <Link
              href={resumeRoute}
              className="font-semibold text-emerald-400 underline"
            >
              Resume Assessment →
            </Link>
          </p>
        )}
      </form>
    </GuestLayout>
  );
}
