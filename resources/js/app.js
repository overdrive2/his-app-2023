import './bootstrap';

import Alpine from 'alpinejs';
import focus from '@alpinejs/focus';
import {
    Modal,
    Input,
    Sidenav,
    Dropdown,
    Ripple,
    initTE,
    Select,
    Datepicker,
    Timepicker
  } from "tw-elements";

initTE({ Dropdown, Modal, Input , Ripple, Sidenav, Select, Datepicker, Timepicker });

window.Modal = Modal;
window.Datepicker = Datepicker;
window.Timepicker = Timepicker;
window.Alpine = Alpine;

Alpine.plugin(focus);

Alpine.start();
