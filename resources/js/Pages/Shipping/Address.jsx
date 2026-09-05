import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';

const fieldClasses =
  'mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 shadow-sm focus:border-emerald-400 focus:ring-emerald-400';

const labelClasses =
  'block text-xs font-medium uppercase tracking-wide text-neutral-400';

export default function Address({ address }) {
  const { data, setData, post, processing, errors } = useForm({
    full_name: address.full_name,
    email: address.email,
    phone: address.phone,
    address_line1: address.address_line1,
    city: address.city,
    state: address.state,
    postal_code: address.postal_code,
  });

  const submit = (e) => {
    e.preventDefault();
    post(route('shipping.store'));
  };

  return (
    <GuestLayout>
      <Head title="Shipping address" />

      <h1 className="text-center text-2xl font-bold text-white">
        Shipping address
      </h1>
      <p className="mt-2 text-center text-sm text-neutral-400">
        Basic validation happens here. Final restrictions are set by the
        pharmacy partner.
      </p>

      <form onSubmit={submit} className="mt-8 space-y-4">
        <div>
          <label className={labelClasses}>Full Name</label>
          <input
            value={data.full_name}
            className={fieldClasses}
            placeholder="Enter full name"
            onChange={(e) => setData('full_name', e.target.value)}
          />
          <InputError message={errors.full_name} className="mt-2" />
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className={labelClasses}>Email Address</label>
            <input
              type="email"
              value={data.email}
              className={fieldClasses}
              placeholder="Enter email address"
              onChange={(e) => setData('email', e.target.value)}
            />
            <InputError message={errors.email} className="mt-2" />
          </div>

          <div>
            <label className={labelClasses}>Mobile Phone</label>
            <input
              value={data.phone}
              className={fieldClasses}
              placeholder="Enter mobile number"
              onChange={(e) => setData('phone', e.target.value)}
            />
            <InputError message={errors.phone} className="mt-2" />
          </div>
        </div>

        <div>
          <label className={labelClasses}>Residential Street Address</label>
          <input
            value={data.address_line1}
            className={fieldClasses}
            placeholder="Enter street address"
            onChange={(e) => setData('address_line1', e.target.value)}
          />
          <InputError message={errors.address_line1} className="mt-2" />
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div>
            <label className={labelClasses}>City</label>
            <input
              value={data.city}
              className={fieldClasses}
              placeholder="Enter city"
              onChange={(e) => setData('city', e.target.value)}
            />
            <InputError message={errors.city} className="mt-2" />
          </div>

          <div>
            <label className={labelClasses}>State</label>
            <input
              value={data.state}
              className={fieldClasses}
              placeholder="Enter state"
              onChange={(e) => setData('state', e.target.value)}
            />
            <InputError message={errors.state} className="mt-2" />
          </div>
        </div>

        <div>
          <label className={labelClasses}>ZIP Code</label>
          <input
            value={data.postal_code}
            className={fieldClasses}
            placeholder="Enter zip code"
            onChange={(e) => setData('postal_code', e.target.value)}
          />
          <InputError message={errors.postal_code} className="mt-2" />
        </div>

        <button
          type="submit"
          disabled={processing}
          className="w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
        >
          Continue
        </button>
      </form>
    </GuestLayout>
  );
}
