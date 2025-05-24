import './bootstrap';

import Alpine from 'alpinejs';
import persist from '@alpinejs/persist'; // Import the persist plugin

Alpine.plugin(persist); // Initialize the persist plugin

window.Alpine = Alpine;

Alpine.start();
