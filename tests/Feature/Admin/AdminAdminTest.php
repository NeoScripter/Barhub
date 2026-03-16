<?php

declare(strict_types=1);

describe('Admin Company Test - Access Control', function (): void {
    it('allows SUPER ADMIN to enter the index page');
    it('allows SUPER ADMIN to add an admin');
    it('allows SUPER ADMIN to delete an admin');
    it('forbids ADMIN from entering the index page');
    it('forbids ADMIN from adding an admin');
    it('forbids ADMIN from deleting an admin');
    it('forbids EXPONENT from entering the index page');
    it('forbids EXPONENT from adding an admin');
    it('forbids EXPONENT from deleting an admin');
    it('forbids USER from entering the index page');
    it('forbids USER from adding an admin');
    it('forbids USER from deleting an admin');
    it('forbids guest users from entering the index page');
    it('forbids guest users from adding an admin');
    it('forbids guest users from deleting an admin');
    it('can assign one admin to multiple exhibitions');
    it('does not downgrade the admin to the user after deleting them if they still have another exhibition assinged');
    it('downgrades the admin to the user after deleting them if they do not have another exhibition assinged');
    it('upgrades the user to the admin after assigning an exhibition to it');
});
