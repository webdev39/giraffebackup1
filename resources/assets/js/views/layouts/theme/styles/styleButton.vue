<script>
    import styled from 'vue-styled-components';

    const themeProps = {
        bgColor:        String,
        bgColor_hover:  String,
        color:          String,
        color_hover:    String,
        border:         String,
        border_hover:   String,
        fill:           String,
        fill_hover:     String,
        important:      String,
    };

    const styleForm = styled('button', themeProps)`
        background-color: ${props => props.bgColor + props.important};
        color:  ${props => props.color + props.important};
        border-color: ${props => props.border + props.important};
        fill: ${props => props.fill + props.important};
        box-shadow: 0 1px 6px rgba(57, 73, 76, 0.35);
        transition: all .3s;
        &:hover {
            background-color: ${props => props.bgColor_hover + props.important};
            color:  ${props => props.color_hover + props.important};
            border-color: ${props => props.border_hover + props.important};
            fill: ${props => props.fill_hover + props.important};
            box-shadow: 0 2px 10px 1px rgba(57, 73, 76, 0.4), 0 1px 2px rgba(57, 73, 76, 0.25);
        }
     `;

    export default styleForm;
</script>