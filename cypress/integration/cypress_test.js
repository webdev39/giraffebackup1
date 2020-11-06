import { name, internet } from 'faker'
const firstName = name.firstName()
const lastName = name.lastName()
const task = firstName + ' ' + lastName
const email = `test+${Date.now()}@test.com`
/// <reference types="Cypress" />
describe('Authorization, task creation and appointment of people', ()=> {
    it('Login', () => {
        cy.clearCookies()

        cy.visit('https://ocstaging.boxibly.com/')

        cy.wait(2000)
        cy.reload(true) // if cache problem

        cy.get('#email')
            .type('qwer@qwer.qwer')
        cy.get('#password')
            .type('secret')
        cy.get('.btn').click()
        cy.wait(2000)
        cy.get('.tour-stop').click()
        cy.wait(2000)
    })
    it('Create_task', () => {
        cy.get('.first-group').click()
        cy.get('.board-name > .board-name-val').click()
        cy.get('.ql-editor')
            .type(task)
        cy.get('#add-task').click()
        cy.wait(2000)
    })
    it('invite user', () => {
        cy.get('[title="Setting group"] > .icon-settings > .icon').click()
        cy.get('.group-member-controls > :nth-child(2)').click()
        cy.get('#email')
            .type(email)
        cy.get('#firstName')
            .type(firstName)
        cy.get('#lastName')
            .type(lastName)
        cy.get('label:has(#global-role-0)').click()
        cy.wait(2000)
        cy.get('#chooseGroup').select('GiraffeSoftware')
        cy.get('#chooseRole').select('Member')
        cy.wait(2000)
        cy.get('label:has(#without_verify)').click()
        cy.get('#invite-user-modal > .v--modal-background-click > .v--modal-box > .modal-content > .modal-footer > .dPMJri').click()
        cy.contains('Save').click()
    })

    it('Assign People', () => {
        cy.get('.drag-list > :nth-child(1) >div>.task-list-item > div  > div   > .task-item-subscribe > button').click({ force: true })
        cy.get(':nth-child(1) > :nth-child(2) > label:has(input) > .checkmark').click()
        cy.get(':nth-child(2) > :nth-child(2) > label:has(input) > .checkmark').click()
        console.log(cy.get('.icon-close'));
        if (cy.get('.icon-close').length > 0) {
            cy.get('.icon-close').click()
        }
    })
})
