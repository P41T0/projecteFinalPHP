import { Passkeys, UserCancelledError } from '@laravel/passkeys';

window.Passkeys = Passkeys;
window.PasskeyUserCancelledError = UserCancelledError;
window.dispatchEvent(new CustomEvent('passkeys:ready'));
