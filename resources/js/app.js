require('./bootstrap');

require("moment-duration-format");
window.moment = require('moment');

//window.ckeditor = require('@ckeditor/ckeditor5-build-classic');

window.tagify = require('@yaireo/tagify');

window.draggable = require('@shopify/draggable');

import Swal from 'sweetalert2';
window.Swal = Swal;

import { Calendar } from '@fullcalendar/core';
import interaction from '@fullcalendar/interaction';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import listPlugin from '@fullcalendar/list';

window.Fullcalendar = {};
window.Fullcalendar.Calendar = Calendar;
window.Fullcalendar.Plugins = [interaction, dayGridPlugin, timeGridPlugin, listPlugin];

window.ClassicEditor = require('@ckeditor/ckeditor5-build-classic');

window.Uppy = require('@uppy/core')
window.DragDrop = require('@uppy/drag-drop')
window.ProgressBar = require('@uppy/progress-bar')
window.FileInput = require('@uppy/file-input')
window.XHRUpload = require('@uppy/xhr-upload')