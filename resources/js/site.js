// Light bundle for pages without Livewire: only Alpine (menu, copy buttons).
import Alpine from 'alpinejs';
import copy from './copy';

Alpine.data('copy', copy);
window.Alpine = Alpine;
Alpine.start();
