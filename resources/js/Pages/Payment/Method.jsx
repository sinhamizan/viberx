import GuestLayout from '@/Layouts/GuestLayout';
import { Head, router } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';

const fieldClasses =
    'mt-1 block w-full rounded-md border border-neutral-700 bg-neutral-800 px-3 py-2 text-white placeholder-neutral-500 shadow-sm focus-within:border-emerald-400';

const labelClasses =
    'block text-xs font-medium uppercase tracking-wide text-neutral-400';

const ELEMENT_STYLE = {
    base: {
        color: '#ffffff',
        fontSize: '16px',
        '::placeholder': { color: '#737373' },
    },
    invalid: { color: '#f87171' },
};

function loadStripeJs() {
    return new Promise((resolve) => {
        if (window.Stripe) {
            resolve(window.Stripe);

            return;
        }

        const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.onload = () => resolve(window.Stripe);
        document.head.appendChild(script);
    });
}

function formatDollars(cents) {
    return (cents / 100).toLocaleString('en-US', {
        style: 'currency',
        currency: 'USD',
    });
}

export default function Method({ stripeKey, clientSecret, estimatedTotalCents }) {
    const [cardholderName, setCardholderName] = useState('');
    const [billingZip, setBillingZip] = useState('');
    const [errorMessage, setErrorMessage] = useState('');
    const [processing, setProcessing] = useState(false);

    const stripeRef = useRef(null);
    const cardNumberElRef = useRef(null);
    const cardExpiryElRef = useRef(null);
    const cardCvcElRef = useRef(null);

    const cardNumberMountRef = useRef(null);
    const cardExpiryMountRef = useRef(null);
    const cardCvcMountRef = useRef(null);

    useEffect(() => {
        let mounted = true;

        loadStripeJs().then((Stripe) => {
            if (!mounted) {
                return;
            }

            const stripe = Stripe(stripeKey);
            const elements = stripe.elements();

            const cardNumber = elements.create('cardNumber', {
                style: ELEMENT_STYLE,
            });
            const cardExpiry = elements.create('cardExpiry', {
                style: ELEMENT_STYLE,
            });
            const cardCvc = elements.create('cardCvc', {
                style: ELEMENT_STYLE,
            });

            cardNumber.mount(cardNumberMountRef.current);
            cardExpiry.mount(cardExpiryMountRef.current);
            cardCvc.mount(cardCvcMountRef.current);

            stripeRef.current = stripe;
            cardNumberElRef.current = cardNumber;
            cardExpiryElRef.current = cardExpiry;
            cardCvcElRef.current = cardCvc;
        });

        return () => {
            mounted = false;
            cardNumberElRef.current?.unmount();
            cardExpiryElRef.current?.unmount();
            cardCvcElRef.current?.unmount();
        };
    }, [stripeKey]);

    const submit = async (e) => {
        e.preventDefault();
        setErrorMessage('');
        setProcessing(true);

        const result = await stripeRef.current.confirmCardSetup(clientSecret, {
            payment_method: {
                card: cardNumberElRef.current,
                billing_details: {
                    name: cardholderName,
                    address: { postal_code: billingZip },
                },
            },
        });

        if (result.error) {
            setErrorMessage(result.error.message);
            setProcessing(false);

            return;
        }

        router.post(
            route('payment.store'),
            {
                setup_intent_id: result.setupIntent.id,
                payment_method_id: result.setupIntent.payment_method,
            },
            { onFinish: () => setProcessing(false) },
        );
    };

    return (
        <GuestLayout>
            <Head title="Payment method" />

            <h1 className="text-center text-2xl font-bold text-white">
                Choose a payment method
            </h1>
            <p className="mt-2 text-center text-sm text-neutral-400">
                Your card may be validated now but is only charged after a
                licensed provider approves your plan and you confirm the
                final order.
            </p>

            {estimatedTotalCents != null && (
                <p className="mt-2 text-center text-sm text-neutral-500">
                    Estimated first charge:{' '}
                    <span className="text-white">
                        {formatDollars(estimatedTotalCents)}
                    </span>
                </p>
            )}

            <form onSubmit={submit} className="mt-8 space-y-4">
                <div>
                    <label className={labelClasses}>
                        Cardholder Full Name
                    </label>
                    <input
                        value={cardholderName}
                        placeholder="Name on card"
                        className="mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                        onChange={(e) => setCardholderName(e.target.value)}
                    />
                </div>

                <div>
                    <label className={labelClasses}>Card Number</label>
                    <div ref={cardNumberMountRef} className={fieldClasses} />
                </div>

                <div className="grid grid-cols-2 gap-4">
                    <div>
                        <label className={labelClasses}>Expiry Date</label>
                        <div
                            ref={cardExpiryMountRef}
                            className={fieldClasses}
                        />
                    </div>

                    <div>
                        <label className={labelClasses}>CVC</label>
                        <div ref={cardCvcMountRef} className={fieldClasses} />
                    </div>
                </div>

                <div>
                    <label className={labelClasses}>Billing ZIP</label>
                    <input
                        value={billingZip}
                        placeholder="e.g. 94103"
                        className="mt-1 block w-full rounded-md border-neutral-700 bg-neutral-800 text-white placeholder-neutral-500 shadow-sm focus:border-emerald-400 focus:ring-emerald-400"
                        onChange={(e) => setBillingZip(e.target.value)}
                    />
                </div>

                {errorMessage && (
                    <p className="text-sm text-red-400">{errorMessage}</p>
                )}

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
