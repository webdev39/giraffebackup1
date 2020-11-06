<script>
    import styled from 'vue-styled-components';

    const themeProps = { bgColor: String, color: String };
    const styleSidebar = styled('div', themeProps)`
        background-color: ${props => props.bgColor};
        color: ${props => props.color};
        fill: ${props => props.color};
     `;

    export default styleSidebar;
</script>