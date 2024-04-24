document.addEventListener('DOMContentLoaded', function() {
    // Define tour steps
    const tour = new Shepherd.Tour({
        defaultStepOptions: {
            cancelIcon: {
                enabled: true
            },
            classes: 'shepherd-theme-arrows',
            scrollTo: { behavior: 'smooth', block: 'center' }
        }
    });
    
    tour.addStep({
        title: '1. Welcome to Moodle LTE Plugin!',
        text: 'The Moodle LTE plugin enables seamless integration between your WordPress website and your Moodle Learning Management System (LMS). This integration empowers you to effectively promote your courses by leveraging the powerful features of WordPress.',
        attachTo: {
            element: '.moodle-lte-welcome',
            on: 'bottom'
        },
        buttons: [
            {
                action() {
                    return this.next();
                },
                text: 'Next'
            }
        ],
        id: 'step-1'
    });
    
    tour.addStep({
        title: '2. Start Plugin Configuration',
        text: 'Please start configuration!',
        attachTo: {
            element: '.moodle-lte-config',
            on: 'top'
        },
        buttons: [
            {
                action() {
                    return this.back();
                },
                text: 'Back'
            },
            {
                action() {
                    return this.next();
                },
                text: 'Next'
            }
        ],
        id: 'step-2'
    });
    
    tour.addStep({
        title: '3. Enter Moodle LMS URL',
        text: 'Please enter your Moodle LMS URL.',
        attachTo: {
            element: '.moodle-lte-lms-url',
            on: 'top'
        },
        buttons: [
            {
                action() {
                    return this.back();
                },
                text: 'Back'
            },
            {
                action() {
                    return this.next();
                },
                text: 'Next'
            }
        ],
        id: 'step-3'
    });
    
    tour.addStep({
        title: '4. Enter Moodle LMS Access Token',
        text: 'Please enter your Moodle LMS access token.',
        attachTo: {
            element: '.moodle-lte-lms-token',
            on: 'top'
        },
        buttons: [
            {
                action() {
                    return this.back();
                },
                text: 'Back'
            },
            {
                action() {
                    return this.next();
                },
                text: 'Next'
            }
        ],
        id: 'step-4'
    });
    
    tour.addStep({
        title: '5. Enter Moodle LMS Service Name',
        text: 'Please enter the Moodle LMS service name to enrol users.',
        attachTo: {
            element: '.moodle-lte-lms-service-name',
            on: 'top'
        },
        buttons: [
            {
                action() {
                    return this.back();
                },
                text: 'Back'
            },
            {
                action() {
                    return this.next();
                },
                text: 'Next'
            }
        ],
        id: 'step-5'
    });
    
    tour.addStep({
        title: '6. Submit to Save Configurations',
        text: 'Please submit to save configurations!',
        attachTo: {
            element: '#submit',
            on: 'top'
        },
        buttons: [
            {
                action() {
                    return this.back();
                },
                text: 'Back'
            },
            {
                action() {
                    return this.complete();
                },
                text: 'Finish'
            }
        ],
        id: 'step-6'
    });
    
    // Start tour    
    tour.start();    
});