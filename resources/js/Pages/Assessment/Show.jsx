import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';

const fieldClasses =
  'mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 shadow-sm focus:border-emerald-400 focus:ring-emerald-400';

const labelClasses =
  'block text-xs font-medium uppercase tracking-wide text-neutral-400';

const HEALTH_CONDITIONS = [
  { value: 'diabetes', label: 'Diabetes' },
  { value: 'hypertension', label: 'Hypertension' },
  { value: 'heart_disease', label: 'Heart disease' },
  { value: 'liver_or_kidney_disease', label: 'Liver or kidney disease' },
  {
    value: 'pregnant_or_breastfeeding',
    label: 'Pregnant or breastfeeding',
  },
  { value: 'mental_health_condition', label: 'Mental health condition' },
  { value: 'none', label: 'None of the above' },
];

const NOTES_SECTIONS = [
  {
    key: 'medical_history',
    number: 2,
    title: 'Medical History',
    prompt:
      'Describe any past or current medical conditions, surgeries, or relevant family history.',
  },
  {
    key: 'medications',
    number: 3,
    title: 'Current Medications',
    prompt:
      'List any medications you’re currently taking, including dosage if known. Write "None" if not applicable.',
  },
  {
    key: 'allergies',
    number: 4,
    title: 'Allergies',
    prompt:
      'List any known allergies (medication, food, or other). Write "None" if not applicable.',
  },
  {
    key: 'prior_treatments',
    number: 5,
    title: 'Prior Treatments',
    prompt:
      "Describe any treatments you've tried before for this condition, and the outcome.",
  },
];

function withDefaults(answers) {
  return {
    personal_info: {
      date_of_birth: answers.personal_info?.date_of_birth ?? '',
      sex: answers.personal_info?.sex ?? '',
      height_in: answers.personal_info?.height_in ?? '',
      weight_lb: answers.personal_info?.weight_lb ?? '',
      address_line1: answers.personal_info?.address_line1 ?? '',
      city: answers.personal_info?.city ?? '',
      state: answers.personal_info?.state ?? '',
      postal_code: answers.personal_info?.postal_code ?? '',
    },
    medical_history: { notes: answers.medical_history?.notes ?? '' },
    medications: { notes: answers.medications?.notes ?? '' },
    allergies: { notes: answers.allergies?.notes ?? '' },
    prior_treatments: { notes: answers.prior_treatments?.notes ?? '' },
    health_conditions: {
      conditions: answers.health_conditions?.conditions ?? [],
    },
    goals: { notes: answers.goals?.notes ?? '' },
  };
}

export default function Show({ answers }) {
  const { data, setData, post, processing, errors } = useForm(
    withDefaults(answers),
  );

  const submit = (e) => {
    e.preventDefault();
    post(route('assessment.store'));
  };

  const setPersonalInfo = (field, value) => {
    setData('personal_info', { ...data.personal_info, [field]: value });
  };

  const setNotes = (section, value) => {
    setData(section, { notes: value });
  };

  const toggleCondition = (value) => {
    const current = data.health_conditions.conditions;

    if (value === 'none') {
      setData('health_conditions', {
        conditions: current.includes('none') ? [] : ['none'],
      });

      return;
    }

    const withoutNone = current.filter((c) => c !== 'none');

    setData('health_conditions', {
      conditions: withoutNone.includes(value)
        ? withoutNone.filter((c) => c !== value)
        : [...withoutNone, value],
    });
  };

  return (
    <GuestLayout>
      <Head title="Medical questionnaire" />

      <h1 className="text-center text-2xl font-bold text-white">
        Medical questionnaire
      </h1>
      <p className="mt-2 text-center text-sm text-neutral-400">
        Provider-required intake. Conditional questions appear based on your
        answers.
      </p>

      <form onSubmit={submit} className="mt-8 space-y-8">
        <section>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-white">
            1. Personal Information
          </h2>

          <div className="mt-3 space-y-4">
            <div>
              <label className={labelClasses}>Date of Birth</label>
              <input
                type="date"
                value={data.personal_info.date_of_birth}
                className={fieldClasses}
                onChange={(e) =>
                  setPersonalInfo('date_of_birth', e.target.value)
                }
              />
              <InputError
                message={errors['personal_info.date_of_birth']}
                className="mt-2"
              />
            </div>

            <div>
              <label className={labelClasses}>Sex</label>
              <select
                value={data.personal_info.sex}
                className={fieldClasses}
                onChange={(e) => setPersonalInfo('sex', e.target.value)}
              >
                <option value="">Select</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
              </select>
              <InputError
                message={errors['personal_info.sex']}
                className="mt-2"
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className={labelClasses}>Height (inches)</label>
                <input
                  type="number"
                  value={data.personal_info.height_in}
                  className={fieldClasses}
                  onChange={(e) => setPersonalInfo('height_in', e.target.value)}
                />
                <InputError
                  message={errors['personal_info.height_in']}
                  className="mt-2"
                />
              </div>

              <div>
                <label className={labelClasses}>Weight (lb)</label>
                <input
                  type="number"
                  value={data.personal_info.weight_lb}
                  className={fieldClasses}
                  onChange={(e) => setPersonalInfo('weight_lb', e.target.value)}
                />
                <InputError
                  message={errors['personal_info.weight_lb']}
                  className="mt-2"
                />
              </div>
            </div>

            <div>
              <label className={labelClasses}>Residential Street Address</label>
              <input
                value={data.personal_info.address_line1}
                className={fieldClasses}
                onChange={(e) =>
                  setPersonalInfo('address_line1', e.target.value)
                }
              />
              <InputError
                message={errors['personal_info.address_line1']}
                className="mt-2"
              />
            </div>

            <div className="grid grid-cols-2 gap-4">
              <div>
                <label className={labelClasses}>City</label>
                <input
                  value={data.personal_info.city}
                  className={fieldClasses}
                  onChange={(e) => setPersonalInfo('city', e.target.value)}
                />
                <InputError
                  message={errors['personal_info.city']}
                  className="mt-2"
                />
              </div>

              <div>
                <label className={labelClasses}>State</label>
                <input
                  value={data.personal_info.state}
                  className={fieldClasses}
                  onChange={(e) => setPersonalInfo('state', e.target.value)}
                />
                <InputError
                  message={errors['personal_info.state']}
                  className="mt-2"
                />
              </div>
            </div>

            <div>
              <label className={labelClasses}>ZIP Code</label>
              <input
                value={data.personal_info.postal_code}
                className={fieldClasses}
                onChange={(e) => setPersonalInfo('postal_code', e.target.value)}
              />
              <InputError
                message={errors['personal_info.postal_code']}
                className="mt-2"
              />
            </div>
          </div>
        </section>

        {NOTES_SECTIONS.map((section) => (
          <section key={section.key}>
            <h2 className="text-sm font-semibold uppercase tracking-wide text-white">
              {section.number}. {section.title}
            </h2>

            <div className="mt-3">
              <label className={labelClasses}>{section.prompt}</label>
              <textarea
                rows={4}
                value={data[section.key].notes}
                className={fieldClasses}
                onChange={(e) => setNotes(section.key, e.target.value)}
              />
              <InputError
                message={errors[`${section.key}.notes`]}
                className="mt-2"
              />
            </div>
          </section>
        ))}

        <section>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-white">
            6. Health Conditions
          </h2>

          <div className="mt-3 space-y-2">
            <p className={labelClasses}>Select any that apply to you</p>
            {HEALTH_CONDITIONS.map((condition) => (
              <label
                key={condition.value}
                className="flex items-center gap-2 text-sm text-neutral-300"
              >
                <input
                  type="checkbox"
                  checked={data.health_conditions.conditions.includes(
                    condition.value,
                  )}
                  onChange={() => toggleCondition(condition.value)}
                  className="rounded border-neutral-700 bg-neutral-800 text-emerald-400 focus:ring-emerald-400"
                />
                {condition.label}
              </label>
            ))}
            <InputError
              message={errors['health_conditions.conditions']}
              className="mt-2"
            />
          </div>
        </section>

        <section>
          <h2 className="text-sm font-semibold uppercase tracking-wide text-white">
            7. Goals
          </h2>

          <div className="mt-3">
            <label className={labelClasses}>
              What are you hoping to achieve with this treatment?
            </label>
            <textarea
              rows={4}
              value={data.goals.notes}
              className={fieldClasses}
              onChange={(e) => setNotes('goals', e.target.value)}
            />
            <InputError message={errors['goals.notes']} className="mt-2" />
          </div>
        </section>

        <button
          type="submit"
          disabled={processing}
          className="w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
        >
          Submit assessment
        </button>
      </form>
    </GuestLayout>
  );
}
