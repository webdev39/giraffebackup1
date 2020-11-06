<script>
    import styled from 'vue-styled-components';

    const themeProps = { bgColor: String, color: {type: String, default: 'inherit'}};
    const styleCheckbox = styled('span', themeProps)`
        background-color: ${props => props.bgColor};
        &:before {
            color: ${props => props.color}
        }
     `;

    export default styleCheckbox;
</script>