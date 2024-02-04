<script>
    var wpwlOptions = {
        iframeStyles: {
            'card-number-placeholder': {
                'color': '#ff0000',
                'font-size': '16px',
                'font-family': 'monospace'
            },
            'cvv-placeholder': {
                'color': '#0000ff',
                'font-size': '16px',
                'font-family': 'Arial'
            }
        },
        @if(str_contains(mb_strtoupper($brand),'APPLEPAY'))
        applePay: {
            displayName: "{{ config('app.name') }}",
            total: {label: "{{ config('app.name') }}, INC."},
            billingContact: {
                addressLines: ["N/A"],
                locality: "N/A",
                administrativeArea: "SA",
                postalCode: "",
                countryCode: "SA",
                familyName: "COMPANY, INC."
            }
        },
        @endif
    }
</script>
<script async src="{{ $checkoutUrl }}"></script>

<form action="{{ $redirect_url }}" class="paymentWidgets" data-brands="{{ $brand }}"></form>
