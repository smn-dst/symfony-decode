describe('Smoke E2E', () => {
  it("l'endpoint /health répond 200", () => {
    cy.request('/health').then((response) => {
      expect(response.status).to.eq(200);
      // éventuellement : expect(response.headers['content-type']).to.include('application/json');
    });
  });
});