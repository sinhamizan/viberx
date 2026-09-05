import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';

export default function Dashboard() {
  return (
    <AuthenticatedLayout
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          Dashboard
        </h2>
      }
    >
      <Head title="Dashboard" />

      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div className="p-6 text-gray-900">You're logged in!</div>
          </div>

          <div className="mt-6 overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div className="flex items-center justify-between p-6">
              <div>
                <h3 className="text-lg font-medium text-gray-900">
                  Start your treatment
                </h3>
                <p className="mt-1 text-sm text-gray-600">
                  Choose a plan and complete your medical assessment.
                </p>
              </div>

              <Link
                href={route('state.show')}
                className="rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
              >
                Start Assessment
              </Link>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}
