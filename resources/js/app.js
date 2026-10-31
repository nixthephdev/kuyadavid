// Pages with Livewire forms (help, prayer wall, contact, admin). Livewire bundles Alpine.
import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';
import copy from './copy';

Alpine.data('copy', copy);
Livewire.start();
