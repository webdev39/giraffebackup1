<script>
    import styled from 'vue-styled-components';

    const themeProps = { bgColor: String, color: {type: String, default: 'inherit'}, fill: {type: String, default: 'inherit'} };
    const styleContainer = styled('tr', themeProps)`
        background-color: ${props => props.bgColor};
        color: ${props => props.color};
        fill: ${props => props.fill};
     `;

    export default styleContainer;
</script>
