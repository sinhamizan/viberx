import InputError from '@/Components/InputError';
import GuestLayout from '@/Layouts/GuestLayout';
import { Head, useForm } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

const CODE_LENGTH = 6;
const RESEND_COOLDOWN_SECONDS = 30;

export default function VerifyOtp({ email, status }) {
  const [digits, setDigits] = useState(Array(CODE_LENGTH).fill(''));
  const [secondsLeft, setSecondsLeft] = useState(RESEND_COOLDOWN_SECONDS);
  const inputRefs = useRef([]);

  const { setData, post, processing, errors, clearErrors } = useForm({
    code: '',
  });
  const { post: resend, processing: resending } = useForm();

  useEffect(() => {
    setData('code', digits.join(''));
  }, [digits]);

  useEffect(() => {
    if (secondsLeft === 0) {
      return;
    }

    const timer = setTimeout(() => setSecondsLeft((s) => s - 1), 1000);

    return () => clearTimeout(timer);
  }, [secondsLeft]);

  const focusInput = (index) => {
    inputRefs.current[index]?.focus();
  };

  const setDigitAt = (index, value) => {
    setDigits((current) => {
      const next = [...current];
      next[index] = value;

      return next;
    });
  };

  const handleChange = (index, e) => {
    const value = e.target.value.replace(/\D/g, '');

    if (!value) {
      setDigitAt(index, '');

      return;
    }

    setDigitAt(index, value.at(-1));

    if (index < CODE_LENGTH - 1) {
      focusInput(index + 1);
    }
  };

  const handleKeyDown = (index, e) => {
    if (e.key === 'Backspace' && !digits[index] && index > 0) {
      focusInput(index - 1);
    }
  };

  const handlePaste = (e) => {
    const pasted = e.clipboardData
      .getData('text')
      .replace(/\D/g, '')
      .slice(0, CODE_LENGTH);

    if (!pasted) {
      return;
    }

    e.preventDefault();
    setDigits(Array.from({ length: CODE_LENGTH }, (_, i) => pasted[i] ?? ''));
    focusInput(Math.min(pasted.length, CODE_LENGTH - 1));
  };

  const submit = (e) => {
    e.preventDefault();
    clearErrors();

    post(route('otp.verify'));
  };

  const submitResend = () => {
    if (secondsLeft > 0 || resending) {
      return;
    }

    resend(route('otp.resend'), {
      onSuccess: () => {
        setDigits(Array(CODE_LENGTH).fill(''));
        setSecondsLeft(RESEND_COOLDOWN_SECONDS);
        focusInput(0);
      },
    });
  };

  return (
    <GuestLayout>
      <Head title="Verify your account" />

      <h1 className="text-center text-2xl font-bold text-white">
        Verify your account
      </h1>
      <p className="mt-2 text-center text-sm text-neutral-400">
        We sent a 6-digit verification code to{' '}
        <span className="text-neutral-200">{email}</span>. Enter it below to
        verify your account.
      </p>

      {status && (
        <div className="mt-4 text-center text-sm font-medium text-emerald-400">
          {status}
        </div>
      )}

      <form onSubmit={submit} className="mt-8">
        <label className="block text-center text-xs font-medium uppercase tracking-wide text-neutral-400">
          Verification Code
        </label>

        <div className="mt-3 flex justify-center gap-2">
          {digits.map((digit, index) => (
            <input
              key={index}
              ref={(el) => (inputRefs.current[index] = el)}
              type="text"
              inputMode="numeric"
              maxLength={1}
              value={digit}
              autoFocus={index === 0}
              onChange={(e) => handleChange(index, e)}
              onKeyDown={(e) => handleKeyDown(index, e)}
              onPaste={handlePaste}
              className="h-14 w-12 rounded-md border-neutral-700 bg-neutral-800 text-center text-lg text-white shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
            />
          ))}
        </div>

        <InputError message={errors.code} className="mt-3 text-center" />

        <div className="mt-4 text-center text-sm">
          <button
            type="button"
            onClick={submitResend}
            disabled={secondsLeft > 0 || resending}
            className="text-emerald-400 underline disabled:pointer-events-none disabled:text-neutral-500 disabled:no-underline"
          >
            {secondsLeft > 0
              ? `Resend code in ${secondsLeft}s`
              : "Didn't receive the code? Resend code"}
          </button>
          <p className="mt-1 text-xs text-neutral-500">
            The code expires in 10 minutes.
          </p>
        </div>

        <button
          type="submit"
          disabled={processing || digits.some((digit) => digit === '')}
          className="mt-6 w-full rounded-md bg-emerald-300 py-3 text-sm font-semibold uppercase tracking-wide text-neutral-900 transition hover:bg-emerald-200 disabled:opacity-40"
        >
          Verify account
        </button>
      </form>
    </GuestLayout>
  );
}
